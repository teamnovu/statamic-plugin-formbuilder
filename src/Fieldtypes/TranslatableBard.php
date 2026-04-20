<?php

namespace Teamnovu\Formbuilder\Fieldtypes;

use Statamic\Facades\Site;
use Statamic\Fields\Fieldtype;

class TranslatableBard extends Fieldtype
{
    protected $selectableInForms = false;

    protected $selectable = false;

    // protected $categories = ['text'];

    public static function title()
    {
        return __('formbuilder::form.title.translatable_bard');
    }

    protected function configFieldItems(): array
    {
        return [
            [
                'display' => __('Input Behavior'),
                'fields' => [

                    'placeholdertext' => [
                        'display' => __('Label English'),
                        'type' => 'text',
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
        return $value;
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
        $site = auth()->user()->preferredLocale();

        // get all sites
        $sites = Site::all();

        return [
            'site' => $site,
            'sites' => $sites,
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
