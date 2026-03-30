<?php

namespace Teamnovu\Formbuilder\Fieldtypes;

use Statamic\Fields\Fieldtype;

class InputTelephone extends Fieldtype
{
    protected $selectableInForms = true;

    protected $selectable = false;

    protected $categories = ['text'];

    protected $icon = 'phone-telephone-call';

    public static function title()
    {
        return __('formbuilder::form.title.telephone');
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
                    'placeholder' => [
                        'display' => __('form.placeholder.display'),
                        'instructions' => __('form.placeholder.instruction'),
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

                    'show_country_code' => [
                        'display' => __('form.show_country_code.display'),
                        'instructions' => __('form.show_country_code.instruction'),
                        'type' => 'toggle',
                    ],

                    'default_country_code' => [
                        'display' => __('form.default_country_code.display'),
                        'instructions' => __('form.default_country_code.instruction'),
                        'type' => 'text',
                        'default' => '+41',
                    ],
                    'show_country_code_selector' => [
                        'display' => __('form.show_country_code_selector.display'),
                        'instructions' => __('form.show_country_code_selector.instruction'),
                        'type' => 'toggle',
                        'default' => true,
                        'force_in_config' => true,
                    ],
                    'show_search' => [
                        'display' => __('form.show_search.display'),
                        'instructions' => __('form.show_search.instruction'),
                        'type' => 'toggle',
                    ],
                    'floating_label' => [
                        'display' => __('form.floating_label.display'),
                        'instructions' => __('form.floating_label.instruction'),
                        'type' => 'toggle',
                        'default' => true,
                        'force_in_config' => true,
                    ],
                    'country_codes' => [
                        'display' => __('form.country_code_selector.display'),
                        'instructions' => __('form.country_code_selector.instruction'),
                        'type' => 'array',
                        'default' => [
                            'CH' => '+41',
                            'DE' => '+49',
                            'FR' => '+33',
                            'IT' => '+39',
                            'US' => '+1',
                            'GB' => '+44',
                            'ES' => '+34',
                            'NL' => '+31',
                            'BE' => '+32',
                            'SE' => '+46',
                            'NO' => '+47',
                            'DK' => '+45',
                            'FI' => '+358',
                            'EE' => '+372',
                            'LV' => '+371',
                            'LT' => '+370',
                            'PL' => '+48',
                            'CZ' => '+420',
                            'SK' => '+421',
                            'HU' => '+36',
                            'RO' => '+40',
                            'BG' => '+359',
                        ],
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
