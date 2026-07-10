<?php

namespace Teamnovu\Formbuilder\Fieldtypes;

use Statamic\Fields\Fieldtype;

class InputSlider extends Fieldtype
{
    protected $selectable = false; // disable in global fieldtype list

    protected $selectableInForms = true;

    protected $categories = ['number'];

    protected $icon = 'range';

    public static function title()
    {
        return __('formbuilder::form.title.slider');
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
                    'min' => [
                        'display' => __('formbuilder::form.min.display'),
                        'instructions' => __('formbuilder::form.min.instruction'),
                        'type' => 'integer',
                        'default' => 0,
                    ],
                    'max' => [
                        'display' => __('formbuilder::form.max.display'),
                        'instructions' => __('formbuilder::form.max.instruction'),
                        'type' => 'integer',
                        'default' => 100,
                    ],
                    'default' => [
                        'display' => __('formbuilder::form.default.display'),
                        'instructions' => __('formbuilder::form.default.instruction'),
                        'type' => 'integer',
                    ],
                    'step' => [
                        'display' => __('formbuilder::form.step.display'),
                        'instructions' => __('formbuilder::form.step.instruction'),
                        'type' => 'integer',
                        'default' => 1,
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
        if ($data !== null && is_numeric($data)) {
            return str_contains((string) $data, '.') ? (float) $data : (int) $data;
        }

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
