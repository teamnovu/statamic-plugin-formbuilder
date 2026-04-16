<?php

namespace Teamnovu\Formbuilder\Fieldtypes;

use Statamic\Fields\Fieldtype;

class InputNumber extends Fieldtype
{
    protected $selectableInForms = true;

    protected $selectable = false;

    protected $categories = ['number'];

    protected $icon = 'fieldtype-float';

    public static function title()
    {
        return __('formbuilder::form.title.number');
    }

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
                    'min' => [
                        'display' => __('form.min.display'),
                        'instructions' => __('form.min.instruction'),
                        'type' => 'integer',
                        'default' => 0,
                    ],
                    'max' => [
                        'display' => __('form.max.display'),
                        'instructions' => __('form.max.instruction'),
                        'type' => 'integer',
                        'default' => 100,
                    ],
                    'default' => [
                        'display' => __('form.default.display'),
                        'instructions' => __('form.default.instruction'),
                        'type' => 'integer',
                    ],
                    'step' => [
                        'display' => __('form.step.display'),
                        'instructions' => __('form.step.instruction'),
                        'type' => 'integer',
                        'default' => 1,
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