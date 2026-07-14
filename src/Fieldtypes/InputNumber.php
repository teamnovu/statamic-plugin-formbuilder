<?php

namespace Teamnovu\Formbuilder\Fieldtypes;

use Statamic\Fields\Fieldtype;
use Teamnovu\Formbuilder\Support\FieldConfig;

class InputNumber extends Fieldtype
{
    protected $selectable = false; // disable in global fieldtype list

    protected $selectableInForms = true;

    protected $categories = ['number'];

    protected $icon = 'float';

    public static function title()
    {
        return __('formbuilder::form.title.number');
    }

    protected function configFieldItems(): array
    {
        return FieldConfig::createItems([
            'min' => FieldConfig::min(),
            'max' => FieldConfig::max(),
            'default' => FieldConfig::integerDefault(),
            'step' => FieldConfig::step(),
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
