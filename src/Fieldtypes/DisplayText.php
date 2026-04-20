<?php

namespace Teamnovu\Formbuilder\Fieldtypes;

use Statamic\Fields\Fieldtype;

class DisplayText extends Fieldtype
{
    protected $selectableInForms = true;
    
    protected $selectable = false;

    protected $categories = ['text'];

    protected $icon = 'information';

    public static function title()
    {
        return __('formbuilder::form.title.display_text');
    }

    protected function configFieldItems(): array
    {
        return [
            [
                'display' => __('Display Content'),
                'fields' => [
                    'title' => [
                        'display' => __('form.display_title.display'),
                        'instructions' => __('form.display_title.instruction'),
                        'type' => 'translatable_input',
                    ],
                    'text' => [
                        'display' => __('form.display_text.display'),
                        'instructions' => __('form.display_text.instruction'),
                        'type' => 'translatable_bard',
                    ],
                ],
            ],
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