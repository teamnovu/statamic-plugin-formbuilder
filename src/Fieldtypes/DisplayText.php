<?php

namespace Teamnovu\Formbuilder\Fieldtypes;

use Statamic\Fields\Fieldtype;
use Teamnovu\Formbuilder\Support\FieldConfig;

class DisplayText extends Fieldtype
{
    protected $selectable = false; // disable in global fieldtype list

    protected $selectableInForms = true;

    protected $categories = ['text'];

    protected $icon = 'information';

    public static function title()
    {
        return __('formbuilder::form.title.display_text');
    }

    protected function configFieldItems(): array
    {
        return [
            FieldConfig::section(__('Display Content'), [
                'title' => [
                    'display' => __('formbuilder::form.display_title.display'),
                    'instructions' => __('formbuilder::form.display_title.instruction'),
                    'type' => 'translatable_input',
                ],
                'text' => [
                    'display' => __('formbuilder::form.display_text.display'),
                    'instructions' => __('formbuilder::form.display_text.instruction'),
                    'type' => 'translatable_bard',
                ],
            ]),
        ];
    }

    public function defaultValue()
    {
        return null;
    }

    public function preProcess($data)
    {
        return null;
    }

    public function process($data)
    {
        return null;
    }

    public function preload()
    {
        $locale = auth()->user()->preferredLocale();

        return [
            'locale' => $locale,
        ];
    }

    public function rules(): array
    {
        return [];
    }
}
