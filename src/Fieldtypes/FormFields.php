<?php

namespace Teamnovu\Formbuilder\Fieldtypes;

use Illuminate\Support\Collection;
use Statamic\CP\Column;
use Statamic\Facades\Form;
use Statamic\Fieldtypes\Relationship;
use Statamic\Support\Str;

/**
 * Stack selector over the fields of one form's blueprint, used in the email grid
 * of Configure Form to pick which fields an email lists. The form handle comes
 * from the field config (`form`), which Statamic sends along with each request.
 */
class FormFields extends Relationship
{
    // Fields without a submitted value.
    public const VALUELESS_FIELDTYPES = ['spacer', 'display_text'];

    protected $selectable = false; // disable in global fieldtype list

    protected $canEdit = false;

    protected $canCreate = false;

    protected $canSearch = true;

    protected $icon = 'forms';

    public static function title()
    {
        return __('formbuilder::form.form_fields.title');
    }

    public function getIndexItems($request)
    {
        abort_unless($this->canConfigureForms(), 403);

        $items = $this->formFields()->map(fn ($field) => $this->itemArray($field))->values();

        if ($search = $request->get('search')) {
            $items = $items->filter(fn (array $item) => collect([$item['title'], $item['handle'], $item['type']])
                ->contains(fn ($value) => Str::contains(Str::lower($value), Str::lower($search))))->values();
        }

        if ($sort = $request->get('sort')) {
            $items = $items->sortBy($sort, SORT_NATURAL | SORT_FLAG_CASE, $request->get('order') === 'desc')->values();
        }

        return $items;
    }

    protected function getColumns()
    {
        return [
            Column::make('title')->label(__('formbuilder::form.form_fields.column_title')),
            Column::make('handle')->label(__('formbuilder::form.form_fields.column_handle')),
            Column::make('type')->label(__('formbuilder::form.form_fields.column_type')),
        ];
    }

    // Keep the blueprint order in the selection panel.
    public function initialSortColumn()
    {
        return null;
    }

    protected function authorizeItemData($id): bool
    {
        return $this->canConfigureForms();
    }

    protected function toItemArray($id)
    {
        if ($field = $this->formFields()->get($id)) {
            return $this->itemArray($field);
        }

        return $this->invalidItemArray($id);
    }

    private function itemArray($field): array
    {
        return [
            'id' => $field->handle(),
            'title' => $field->display(),
            'handle' => $field->handle(),
            'type' => $field->fieldtype()->title(),
            'hint' => $field->handle(),
        ];
    }

    private function formFields(): Collection
    {
        $form = Form::find((string) $this->config('form'));

        if (! $form) {
            return collect();
        }

        return $form->blueprint()->fields()->all()
            ->reject(fn ($field) => in_array($field->type(), self::VALUELESS_FIELDTYPES, true));
    }

    private function canConfigureForms(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->isSuper() || $user?->hasPermission('configure forms'));
    }
}
