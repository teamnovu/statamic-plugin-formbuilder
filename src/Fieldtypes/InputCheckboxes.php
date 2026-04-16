<?php

namespace Teamnovu\Formbuilder\Fieldtypes;

use Statamic\Fields\Fieldtype;

class InputCheckboxes extends Fieldtype
{
    protected $selectableInForms = true;

    protected $selectable = false;

    protected $categories = ['controls'];

    protected $icon = 'checkmark';

    public static function title()
    {
        return __('formbuilder::form.title.checkboxes');
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
                        'display' => __('form.label.display'),
                        'instructions' => __('form.label.instruction'),
                        'type' => 'translatable_input',
                    ],
                    'hint' => [
                        'display' => __('form.hint.display'),
                        'instructions' => __('form.hint.instruction'),
                        'type' => 'translatable_input',
                    ],
                    'help' => [
                        'display' => __('form.help.display'),
                        'instructions' => __('form.help.instruction'),
                        'type' => 'translatable_input',
                    ],
                    'orientation' => [
                        'display' => __('form.orientation.display'),
                        'instructions' => __('form.orientation.instruction'),
                        'type' => 'select',
                        'options' => [
                            'horizontal' => __('form.orientation.horizontal'),
                            'vertical' => __('form.orientation.vertical'),
                        ],
                        'default' => 'horizontal',
                        'force_in_config' => true,
                    ],
                    'variant' => [
                        'display' => __('form.variant.display'),
                        'instructions' => __('form.variant.instruction'),
                        'type' => 'select',
                        'options' => [
                            'card' => __('form.variant.card'),
                            'list' => __('form.variant.list'),
                            'table' => __('form.variant.table'),
                        ],
                        'default' => 'list',
                        'force_in_config' => true,
                    ],
                    'indicator' => [
                        'display' => __('form.indicator.display'),
                        'instructions' => __('form.indicator.instruction'),
                        'type' => 'select',
                        'options' => [
                            'start' => __('form.indicator.start'),
                            'end' => __('form.indicator.end'),
                            'hidden' => __('form.indicator.hidden'),
                        ],
                        'default' => 'start',
                        'force_in_config' => true,
                    ],

                    'options' => [
                        'display' => __('form.options.display'),
                        'instructions' => __('form.options.instruction'),
                        'type' => 'grid',
                        'fields' => [
                            ['handle' => 'key', 'field' => ['type' => 'text']],
                            ['handle' => 'text', 'field' => ['type' => 'translatable_bard']],
                        ],
                    ],

                ],
            ],
            // [
            //     'display' => 'Antlers',
            //     'fields' => [
            //         'antlers' => [
            //             'display' => __('Allow Antlers'),
            //             'instructions' => __('statamic::fieldtypes.any.config.antlers'),
            //             'type' => 'toggle',
            //         ],
            //     ],
            // ],
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
        // get the current user locale
        // $locale = app()->getLocale();

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