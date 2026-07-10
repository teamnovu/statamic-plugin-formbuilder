<?php

namespace Teamnovu\Formbuilder\Fieldtypes;

use Statamic\Fields\Fieldtype;

class InputRadioButtons extends Fieldtype
{
    protected $selectable = false; // disable in global fieldtype list

    protected $selectableInForms = true;

    protected $categories = ['controls'];

    protected $icon = 'radio';

    public static function title()
    {
        return __('formbuilder::form.title.radio_buttons');
    }

    // allways save default value
    protected $default = null;

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
                    'orientation' => [
                        'display' => __('formbuilder::form.orientation.display'),
                        'instructions' => __('formbuilder::form.orientation.instruction'),
                        'type' => 'select',
                        'options' => [
                            'horizontal' => __('formbuilder::form.orientation.horizontal'),
                            'vertical' => __('formbuilder::form.orientation.vertical'),
                        ],
                        'default' => 'horizontal',
                        'force_in_config' => true,
                    ],
                    'variant' => [
                        'display' => __('formbuilder::form.variant.display'),
                        'instructions' => __('formbuilder::form.variant.instruction'),
                        'type' => 'select',
                        'options' => [
                            'card' => __('formbuilder::form.variant.card'),
                            'list' => __('formbuilder::form.variant.list'),
                            'table' => __('formbuilder::form.variant.table'),
                        ],
                        'default' => 'list',
                        'force_in_config' => true,
                    ],
                    'indicator' => [
                        'display' => __('formbuilder::form.indicator.display'),
                        'instructions' => __('formbuilder::form.indicator.instruction'),
                        'type' => 'select',
                        'options' => [
                            'start' => __('formbuilder::form.indicator.start'),
                            'end' => __('formbuilder::form.indicator.end'),
                            'hidden' => __('formbuilder::form.indicator.hidden'),
                        ],
                        'default' => 'start',
                        'force_in_config' => true,
                    ],

                    'options' => [
                        'display' => __('formbuilder::form.options.display'),
                        'instructions' => __('formbuilder::form.options.instruction'),
                        'type' => 'grid',
                        'fields' => [
                            ['handle' => 'key', 'field' => ['type' => 'text']],
                            ['handle' => 'text', 'field' => ['type' => 'translatable_bard']],
                        ],
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
            'options' => $this->config('options'),
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
