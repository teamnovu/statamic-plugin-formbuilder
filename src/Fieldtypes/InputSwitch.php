<?php

namespace Teamnovu\Formbuilder\Fieldtypes;

use Statamic\Fields\Fieldtype;
use Teamnovu\Formbuilder\Support\FieldConfig;

class InputSwitch extends Fieldtype
{
    protected $selectable = false; // disable in global fieldtype list

    protected $selectableInForms = true;

    protected $categories = ['controls'];

    protected $icon = 'toggle';

    public static function title()
    {
        return __('formbuilder::form.title.switch');
    }

    // allways save default value
    protected $default = null;

    protected function configFieldItems(): array
    {
        return FieldConfig::createItems([
            'default' => [
                'display' => __('formbuilder::form.default.display'),
                'instructions' => __('formbuilder::form.default.instruction'),
                'type' => 'toggle',
            ],
            'label_deactivated' => [
                'display' => __('formbuilder::form.label_deactivated.display'),
                'instructions' => __('formbuilder::form.label_deactivated.instruction'),
                'type' => 'translatable_input',
            ],
            'label_activated' => [
                'display' => __('formbuilder::form.label_activated.display'),
                'instructions' => __('formbuilder::form.label_activated.instruction'),
                'type' => 'translatable_input',
            ],
        ]);
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
