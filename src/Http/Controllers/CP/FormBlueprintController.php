<?php

namespace Teamnovu\Formbuilder\Http\Controllers\CP;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Statamic\Fields\Blueprint;
use Statamic\Http\Controllers\CP\Fields\ManagesBlueprints;
use Statamic\Http\Controllers\CP\Forms\FormBlueprintController as StatamicFormBlueprintController;
use Teamnovu\Formbuilder\Support\EmailFieldSelection;

/**
 * Keeps the email field selections of a form in step with its blueprint:
 * renamed fields stay selected under their new handle, deleted ones are removed.
 *
 * The blueprint editor gives each field an `_id` that survives a rename. Existing
 * fields get `{tab}-{section}-{index}` when the page loads (ManagesBlueprints::sectionToVue()).
 * The editor keeps those ids after saving without reloading, so the id => handle map
 * of the page load and of each save is remembered in the session for the next save from the same page.
 *
 * Every page load gets its own editor token, which the editor posts back with the
 * blueprint, so several tabs of the same form never share or clear each other's map.
 */
class FormBlueprintController extends StatamicFormBlueprintController
{
    use ManagesBlueprints;

    private const EDITOR_KEY = 'formbuilder_editor';

    private const MAX_EDITORS_PER_FORM = 10;

    public function edit($form)
    {
        $response = parent::edit($form);
        $editor = Str::random(32);

        // Remember the ids the editor was given, so a later save maps them to the handles this editor saw,
        // even when the blueprint was reordered or renamed by someone else in the meantime.
        $this->rememberIds($form, $editor, $this->idsOnLoad($form->blueprint()));

        // The editor keeps unknown keys of the blueprint object and sends them back on save.
        return $response->with('blueprint', [
            ...$this->toVueObject($form->blueprint()),
            self::EDITOR_KEY => $editor,
        ]);
    }

    public function update(Request $request, $form)
    {
        $before = $form->blueprint();
        $handlesBefore = $before->fields()->all()->keys()->all();
        $editor = $this->editorToken($request);
        $idsBefore = ($editor ? session()->get($this->sessionKey($form, $editor)) : null) ?? $this->idsOnLoad($before);

        parent::update($request, $form);

        $posted = $this->postedIds($request->input('tabs', []));
        $handlesAfter = $form->blueprint()->fields()->all()->keys()->all();
        $renames = $this->renames($idsBefore, $posted, $handlesBefore, $handlesAfter);
        // A deleted field whose handle was taken over by a renamed one is gone too, so its selection must not carry over.
        $deleted = array_values(array_unique(array_merge(
            array_diff($handlesBefore, $handlesAfter, array_keys($renames)),
            array_diff(array_intersect(array_values($renames), $handlesBefore), array_keys($renames)),
        )));

        EmailFieldSelection::renameAndPrune($form, $renames, $deleted);

        if ($editor) {
            $this->rememberIds($form, $editor, $posted);
        }
    }

    private function editorToken(Request $request): ?string
    {
        $token = $request->input(self::EDITOR_KEY);

        return is_string($token) && preg_match('/^[A-Za-z0-9]{1,64}$/', $token) ? $token : null;
    }

    private function rememberIds($form, string $editor, array $ids): void
    {
        $key = $this->sessionKey($form);
        $editors = session()->get($key, []);

        unset($editors[$editor]);
        $editors[$editor] = $ids;

        session()->put($key, array_slice($editors, -self::MAX_EDITORS_PER_FORM, null, true));
    }

    // Same id scheme as ManagesBlueprints::sectionToVue().
    private function idsOnLoad(Blueprint $blueprint): array
    {
        $ids = [];

        foreach ($blueprint->tabs() as $tab) {
            foreach ($tab->sections() as $sectionIndex => $section) {
                foreach ($section->contents()['fields'] ?? [] as $index => $field) {
                    if (isset($field['handle'])) {
                        $ids[$tab->handle().'-'.$sectionIndex.'-'.$index] = $field['handle'];
                    }
                }
            }
        }

        return $ids;
    }

    private function postedIds(array $tabs): array
    {
        $ids = [];

        foreach ($tabs as $tab) {
            foreach ($tab['sections'] ?? [] as $section) {
                foreach ($section['fields'] ?? [] as $field) {
                    if (isset($field['_id'], $field['handle']) && ($field['type'] ?? null) !== 'import') {
                        $ids[$field['_id']] = $field['handle'];
                    }
                }
            }
        }

        return $ids;
    }

    /**
     * Renames are resolved by field id: an id whose handle changed moves its selection from
     * the old handle to the new one. A new handle may be one that another field just gave up
     * (a -> b, b -> c) or that belonged to a field deleted in the same save.
     * A handle swap or any other cycle cannot be told apart from ids that no longer match
     * the saved blueprint, so no renames are trusted for this save.
     */
    private function renames(array $idsBefore, array $posted, array $handlesBefore, array $handlesAfter): array
    {
        $moves = [];

        foreach ($posted as $id => $new) {
            $old = $idsBefore[$id] ?? null;

            if ($old !== null && $old !== $new && in_array($old, $handlesBefore, true)) {
                $moves[$old] = $new;
            }
        }

        foreach (array_keys($moves) as $start) {
            $seen = [];

            for ($handle = $start; isset($moves[$handle]); $handle = $moves[$handle]) {
                if (isset($seen[$handle])) {
                    return [];
                }

                $seen[$handle] = true;
            }
        }

        $idsByHandleBefore = array_flip($idsBefore);
        $renames = [];

        foreach ($moves as $old => $new) {
            $old = (string) $old;
            $newIsFree = ! in_array($new, $handlesBefore, true)
                || isset($moves[$new])
                || (isset($idsByHandleBefore[$new]) && ! array_key_exists($idsByHandleBefore[$new], $posted));
            $oldIsGone = ! in_array($old, $handlesAfter, true) || in_array($old, $moves, true);

            if ($oldIsGone && $newIsFree) {
                $renames[$old] = $new;
            }
        }

        return $renames;
    }

    private function sessionKey($form, ?string $editor = null): string
    {
        return 'formbuilder.blueprint_field_ids.'.$form->handle().($editor ? '.'.$editor : '');
    }
}
