<?php

namespace Teamnovu\Formbuilder\Fieldtypes;

use Statamic\Fields\Fieldtype;

class InputText extends Fieldtype
{
    protected $selectable = false; // disable in global fieldtype list

    protected $selectableInForms = true;

    protected $categories = ['text'];

    protected $icon = 'text';

    public static function title()
    {
        return __('formbuilder::form.title.text');
    }

    protected function configFieldItems(): array
    {
        return [
            [
                'display' => __('Input Behavior'),
                'fields' => [
                    'label' => [
                        'display' => __('formbuilder::form.label.display'),
                        'instructions' => __('formbuilder::form.label.instruction'),
                        'type' => 'translatable_input',
                    ],
                    'placeholder' => [
                        'display' => __('formbuilder::form.placeholder.display'),
                        'instructions' => __('formbuilder::form.placeholder.instruction'),
                        'type' => 'translatable_input',
                    ],
                    'help' => [
                        'display' => __('formbuilder::form.help.display'),
                        'instructions' => __('formbuilder::form.help.instruction'),
                        'type' => 'translatable_input',
                    ],
                    'hint' => [
                        'display' => __('formbuilder::form.hint.display'),
                        'instructions' => __('formbuilder::form.hint.instruction'),
                        'type' => 'translatable_input',
                    ],
                    'character_limit' => [
                        'display' => __('formbuilder::form.character_limit.display'),
                        'instructions' => __('formbuilder::form.character_limit.instruction'),
                        'type' => 'integer',
                    ],
                    'floating_label' => [
                        'display' => __('formbuilder::form.floating_label.display'),
                        'instructions' => __('formbuilder::form.floating_label.instruction'),
                        'type' => 'toggle',
                        'default' => true,
                        'force_in_config' => true,
                    ],

                ],
            ],
        ];
    }

    /**
     * The blank/default value.
     *
     * @return null
     */
    public function defaultValue()
    {
        return null;
    }

    /**
     * Pre-process the data before it gets sent to the publish page.
     *
     * @param  mixed  $data
     * @return mixed
     */
    public function preProcess($data)
    {
        return $data;
    }

    /**
     * Process the data before it gets saved.
     *
     * @param  mixed  $data
     * @return mixed
     */
    public function process($data)
    {
        return $data;
    }

    public function preProcessIndex($value)
    {
        if ($value) {
            return $this->config('prepend').$value.$this->config('append');
        }
    }

    /**
     * Preload data for the fieldtype.
     *
     * @return array
     */
    public function preload()
    {
        $locale = auth()->user()->preferredLocale();

        return [
            'locale' => $locale,
        ];
    }

    /**
     * Validation rules for the fieldtype.
     */
    public function rules(): array
    {
        return [];
    }
}
