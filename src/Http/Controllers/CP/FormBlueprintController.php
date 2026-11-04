<?php

namespace Teamnovu\Formbuilder\Http\Controllers\CP;

use Illuminate\Http\Request;
use Statamic\Fields\Blueprint;
use Statamic\Http\Controllers\CP\Forms\FormBlueprintController as StatamicFormBlueprintController;
use Teamnovu\Formbuilder\Support\EmailFieldSelection;

/**
 * Keeps the email field selections of a form in step with its blueprint:
 * renamed fields stay selected under their new handle, deleted ones are removed.
 *
 * The blueprint editor gives each field an `_id` that survives a rename. Existing
 * fields get `{tab}-{section}-{index}` when the page loads (ManagesBlueprints::sectionToVue()).
 * The editor keeps those ids after saving without reloading, so the id => handle map
 * of each save is remembered in the session for the next save from the same page.
 */
class FormBlueprintController extends StatamicFormBlueprintController
{
    public function edit($form)
    {
        session()->forget($this->sessionKey($form));

        return parent::edit($form);
    }

    public function update(Request $request, $form)
    {
        $before = $form->blueprint();
        $handlesBefore = $before->fields()->all()->keys()->all();
        $idsBefore = session()->get($this->sessionKey($form)) ?? $this->idsOnLoad($before);

        parent::update($request, $form);

        $posted = $this->postedIds($request->input('tabs', []));
        $handlesAfter = $form->blueprint()->fields()->all()->keys()->all();
        $renames = $this->renames($idsBefore, $posted, $handlesBefore, $handlesAfter);
        $deleted = array_values(array_diff($handlesBefore, $handlesAfter, array_keys($renames)));

        EmailFieldSelection::renameAndPrune($form, $renames, $deleted);

        session()->put($this->sessionKey($form), $posted);
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
     * A rename is a field id whose handle changed, where the old handle is gone and the
     * new one did not exist before. When an unchanged handle sits under another id, the
     * ids no longer match the saved blueprint, so no renames are trusted for this save.
     */
    private function renames(array $idsBefore, array $posted, array $handlesBefore, array $handlesAfter): array
    {
        $idsByHandleBefore = array_flip($idsBefore);

        foreach ($posted as $id => $handle) {
            if (isset($idsByHandleBefore[$handle]) && (string) $idsByHandleBefore[$handle] !== (string) $id) {
                return [];
            }
        }

        $renames = [];

        foreach ($posted as $id => $new) {
            $old = $idsBefore[$id] ?? null;

            if ($old === null || $old === $new) {
                continue;
            }

            if (in_array($old, $handlesBefore, true) && ! in_array($old, $handlesAfter, true) && ! in_array($new, $handlesBefore, true)) {
                $renames[$old] = $new;
            }
        }

        return $renames;
    }

    private function sessionKey($form): string
    {
        return 'formbuilder.blueprint_field_ids.'.$form->handle();
    }
}
