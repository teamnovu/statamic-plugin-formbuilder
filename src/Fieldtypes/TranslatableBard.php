<?php

namespace Teamnovu\Formbuilder\Fieldtypes;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Statamic\Facades\Site;
use Statamic\Fieldtypes\Bard;

class TranslatableBard extends Bard
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

        // Bard::preload() expects the field's value to be a Bard node structure (each item has a `type` key).
        // Our translatable value shape is different, so we call Bard::preload() with an empty value.
        $originalField = $this->field;
        $this->field = $originalField->newInstance()->setValue([]);

        try {
            $bardPreload = parent::preload();
        } finally {
            $this->field = $originalField;
        }

        return array_merge($bardPreload, [
            'site' => $site,
            'sites' => $sites,
        ]);
    }

    /**
     * Validation rules for the fieldtype.
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Normalize the stored value into the shape the CP fieldtype expects:
     * an array of { handle: string, value: mixed }.
     *
     * Supports legacy/alternate shapes:
     * - null
     * - associative array keyed by site handle
     * - already-normalized array of arrays
     */
    private function normalizeTranslatableValue($data): array
    {
        if ($data === null) {
            return [];
        }

        if ($data instanceof Collection) {
            $data = $data->all();
        }

        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        if (! is_array($data) && is_object($data) && method_exists($data, 'value')) {
            $data = $data->value();
        }

        if (is_string($data)) {
            $decoded = json_decode($data, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $data = $decoded;
            }
        }

        if (! is_array($data)) {
            return [];
        }

        $isSequential = array_is_list($data);

        if (! $isSequential) {
            // Associative array keyed by site handle.
            return collect($data)
                ->map(function ($value, $handle) {
                    return [
                        'handle' => (string) $handle,
                        'value' => $value,
                    ];
                })
                ->values()
                ->all();
        }

        // Sequential array. Attempt to keep entries with handle/value.
        return collect($data)
            ->filter(fn ($entry) => is_array($entry))
            ->map(function (array $entry) {
                $handle = $entry['handle'] ?? null;

                if (! is_string($handle) || $handle === '') {
                    return null;
                }

                return [
                    'handle' => $handle,
                    'value' => $entry['value'] ?? null,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Bard expects a sequential array of nodes (each with a `type` key).
     * Statamic may hand us values as Collections/Arrayables/JSON strings depending on context.
     */
    private function normalizeBardValue($value): array
    {
        if ($value === null) {
            return [];
        }

        if ($value instanceof Collection) {
            return $value->all();
        }

        if ($value instanceof Arrayable) {
            return $value->toArray();
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }

            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        return [];
    }
}
