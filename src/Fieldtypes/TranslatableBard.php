<?php

namespace Teamnovu\Formbuilder\Fieldtypes;

use Statamic\Facades\Site;
use Statamic\Fields\Field;
use Statamic\Fields\Fieldtype;
use Statamic\Fieldtypes\Bard as StatamicBard;

class TranslatableBard extends Fieldtype
{
    protected $selectableInForms = false;

    // protected $categories = ['text'];

    public static function title()
    {
        return __('form.title.translatable_bard');
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
        if (! is_array($data)) {
            return [];
        }

        return collect($data)->map(function ($entry) {
            $handle = $entry['handle'] ?? null;
            $value = $entry['value'] ?? [];

            $bard = $this->makeBardFieldtype($handle, $value);

            return [
                'handle' => $handle,
                'value' => $bard->preProcess($value),
            ];
        })->values()->all();
    }

    /**
     * Process the data before it gets saved.
     *
     * @param  mixed  $data
     * @return mixed
     */
    public function process($data)
    {
        if (! is_array($data)) {
            return [];
        }

        return collect($data)->map(function ($entry) {
            $handle = $entry['handle'] ?? null;
            $value = $entry['value'] ?? [];

            $bard = $this->makeBardFieldtype($handle, $value);

            return [
                'handle' => $handle,
                'value' => $bard->process($value),
            ];
        })->values()->all();
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
        $site = auth()->user()?->preferredLocale() ?? Site::current()->handle();
        $sites = Site::all()->map->toArray();

        $value = $this->field()?->value() ?? [];
        $bardFieldtype = $this->makeBardFieldtype(null, $value);

        $siteMeta = $sites->mapWithKeys(function ($siteItem) use ($value) {
            $siteHandle = $siteItem['handle'] ?? null;
            $siteValue = collect($value)->firstWhere('handle', $siteHandle)['value'] ?? [];
            $bard = $this->makeBardFieldtype($siteHandle, $siteValue);

            return [$siteHandle => $bard->preload()];
        });

        return [
            'site' => $site,
            'sites' => $sites,
            'bardMeta' => $siteMeta,
            'bardConfig' => $bardFieldtype->config(),
        ];
    }

    private function makeBardFieldtype(string $siteHandle = null, $value = null): StatamicBard
    {
        $handle = ($this->field()?->handle() ?? 'translatable_bard').'_bard'.($siteHandle ? "_{$siteHandle}" : '');
        $buttons = ['bold', 'italic', 'anchor'];
        $field = new Field($handle, [
            'type' => 'bard',
            'buttons' => $buttons,
            'save_html' => true,
        ]);
        $field->setValue($value ?? []);

        return app(StatamicBard::class)->setField($field);
    }

    /**
     * Validation rules for the fieldtype.
     */
    public function rules(): array
    {
        return [];
    }
}