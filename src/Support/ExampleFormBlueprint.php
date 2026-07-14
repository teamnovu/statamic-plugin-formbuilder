<?php

namespace Teamnovu\Formbuilder\Support;

use Statamic\Facades\Site;

class ExampleFormBlueprint
{
    /** @var list<string> */
    private array $sites;

    /**
     * @param  list<string>|null  $sites
     */
    public function __construct(?array $sites = null)
    {
        $this->sites = $sites ?? Site::all()->map->handle()->values()->all();

        if ($this->sites === []) {
            $this->sites = ['default'];
        }
    }

    /**
     * @return list<string>
     */
    public function sites(): array
    {
        return $this->sites;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'tabs' => [
                'main' => [
                    'display' => 'Main',
                    'sections' => [
                        [
                            'fields' => $this->fields(),
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return list<array{handle: string, field: array<string, mixed>}>
     */
    private function fields(): array
    {
        return [
            $this->field('input_text', 'input_text', [
                ...$this->commonTranslatableFields(placeholder: true),
                'character_limit' => 25,
                'floating_label' => false,
                'validate' => ['required'],
                'width' => 50,
            ]),
            $this->field('input_email', 'input_email', [
                ...$this->commonTranslatableFields(placeholder: true),
                'floating_label' => false,
                'width' => 50,
            ]),
            $this->field('input_textarea', 'input_textarea', [
                ...$this->commonTranslatableFields(placeholder: true),
                'floating_label' => false,
            ]),
            $this->field('input_phone', 'input_phone', [
                ...$this->commonTranslatableFields(placeholder: true),
                'show_country_code_selector' => false,
                'floating_label' => false,
                'country_codes' => $this->countryCodes(),
                'show_country_code' => false,
                'show_search' => false,
                'width' => 33,
            ]),
            $this->field('input_phone_country_code', 'input_phone', [
                ...$this->commonTranslatableFields(placeholder: true),
                'display' => 'Input Phone (Country Code)',
                'show_country_code_selector' => true,
                'floating_label' => false,
                'country_codes' => $this->countryCodes(),
                'show_country_code' => true,
                'show_search' => false,
                'width' => 33,
            ], label: 'Input Phone (Country Code)'),
            $this->field('input_phone_country_code_search', 'input_phone', [
                ...$this->commonTranslatableFields(placeholder: true),
                'display' => 'Input Phone (Country Code + Search)',
                'show_country_code_selector' => true,
                'floating_label' => false,
                'country_codes' => $this->countryCodes(),
                'show_country_code' => true,
                'show_search' => true,
                'show_country_code_selector_search' => true,
                'width' => 33,
            ], label: 'Input Phone (Country Code + Search)'),
            $this->field('input_number', 'input_number', [
                ...$this->commonTranslatableFields(placeholder: true),
                'width' => 33,
            ]),
            $this->field('input_slider', 'input_slider', [
                ...$this->commonTranslatableFields(),
                'step' => 10,
                'default' => 50,
                'width' => 66,
            ]),
            $this->field('input_select', 'input_select', [
                ...$this->commonTranslatableFields(placeholder: true),
                'options' => $this->plainOptions(),
                'floating_label' => false,
                'width' => 50,
            ]),
            $this->field('input_select_multiple', 'input_select', [
                ...$this->commonTranslatableFields(placeholder: true),
                'display' => 'Input Select (Multiple)',
                'options' => $this->plainOptions(),
                'floating_label' => false,
                'multiple' => true,
                'width' => 50,
            ], label: 'Input Select (Multiple)'),
            $this->field('input_select_search', 'input_select', [
                ...$this->commonTranslatableFields(placeholder: true),
                'display' => 'Input Select (Search)',
                'options' => $this->plainOptions(),
                'floating_label' => false,
                'show_search' => true,
            ], label: 'Input Select (Search)'),
            $this->field('input_date', 'input_date', [
                ...$this->commonTranslatableFields(),
                'width' => 50,
            ]),
            $this->field('input_date_restricted', 'input_date', [
                ...$this->commonTranslatableFields(),
                'display' => 'Input Date (Restricted)',
                'earliest_date' => '2026-04-01',
                'latest_date' => '2026-04-30',
                'width' => 50,
            ], label: 'Input Date (Restricted)'),
            $this->field('input_daterange', 'input_daterange', [
                ...$this->commonTranslatableFields(),
                'width' => 50,
            ]),
            $this->field('input_daterange_restricted', 'input_daterange', [
                ...$this->commonTranslatableFields(),
                'display' => 'Input Daterange (Restricted)',
                'earliest_date' => '2026-04-01',
                'latest_date' => '2026-04-30',
                'width' => 50,
            ], label: 'Input Daterange (Restricted)'),
            $this->field('input_switch', 'input_switch', [
                ...$this->commonTranslatableFields(),
                'label_deactivated' => $this->localized('Label when deactivated'),
                'label_activated' => $this->localized('Label when activated'),
                'width' => 50,
            ]),
            $this->field('input_radio_buttons', 'input_radio_buttons', [
                ...$this->commonTranslatableFields(),
                'options' => $this->richOptions(),
                'orientation' => 'horizontal',
                'variant' => 'list',
                'indicator' => 'start',
                'width' => 50,
            ]),
            $this->field('input_checkboxes', 'input_checkboxes', [
                ...$this->commonTranslatableFields(),
                'options' => $this->richOptions(),
                'orientation' => 'horizontal',
                'width' => 50,
            ]),
            [
                'handle' => 'spacer',
                'field' => [
                    'type' => 'spacer',
                    'display' => 'Spacer',
                    'localizable' => false,
                ],
            ],
            $this->field('input_file_upload', 'input_file_upload', [
                ...$this->commonTranslatableFields(),
                'multiple' => false,
                'max_files' => 1,
                'max_filesize' => 10240,
                'container' => 'assets',
                'icon_label' => $this->localized('Icon Label'),
                'allowed_mimes' => $this->allowedMimes(),
                'width' => 50,
            ]),
            $this->field('input_file_upload_multiple', 'input_file_upload', [
                ...$this->commonTranslatableFields(),
                'display' => 'Input File Upload (Multiple)',
                'multiple' => false,
                'max_files' => 3,
                'max_filesize' => 10240,
                'container' => 'assets',
                'icon_label' => $this->localized('Icon Label'),
                'allowed_mimes' => $this->allowedMimes(),
                'width' => 50,
            ], label: 'Input File Upload (Multiple)'),
            [
                'handle' => 'display_text',
                'field' => [
                    'title' => $this->localized('Display Text Title'),
                    'text' => $this->localized('<p>Example display text body.</p>'),
                    'type' => 'display_text',
                    'display' => 'Display Text',
                    'localizable' => false,
                ],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $config
     * @return array{handle: string, field: array<string, mixed>}
     */
    private function field(string $handle, string $type, array $config, ?string $label = null): array
    {
        $display = $config['display'] ?? $this->displayName($type);

        return [
            'handle' => $handle,
            'field' => [
                'label' => $this->localized($label ?? $display),
                ...$config,
                'type' => $type,
                'display' => $display,
                'localizable' => false,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function commonTranslatableFields(bool $placeholder = false): array
    {
        $fields = [
            'hint' => $this->localized('Hint'),
            'help' => $this->localized('Help'),
        ];

        if ($placeholder) {
            $fields['placeholder'] = $this->localized('Placeholder');
        }

        return $fields;
    }

    /**
     * @return list<array{handle: string, value: string}>
     */
    public function localized(string $value): array
    {
        $entries = [];

        foreach ($this->sites as $index => $handle) {
            $entries[] = [
                'handle' => $handle,
                'value' => $index === 0 ? $value : "{$value} {$handle}",
            ];
        }

        return $entries;
    }

    /**
     * @return list<array{id: string, key: string, text: list<array{handle: string, value: string}>}>
     */
    private function plainOptions(): array
    {
        return [
            ['id' => 'option_1', 'key' => 'option_1', 'text' => $this->localized('Option 1')],
            ['id' => 'option_2', 'key' => 'option_2', 'text' => $this->localized('Option 2')],
            ['id' => 'option_3', 'key' => 'option_3', 'text' => $this->localized('Option 3')],
        ];
    }

    /**
     * @return list<array{id: string, key: string, text: list<array{handle: string, value: string}>}>
     */
    private function richOptions(): array
    {
        return [
            ['id' => 'option_1', 'key' => 'option_1', 'text' => $this->localized('<p>Option 1</p>')],
            ['id' => 'option_2', 'key' => 'option_2', 'text' => $this->localized('<p>Option 2</p>')],
            ['id' => 'option_3', 'key' => 'option_3', 'text' => $this->localized('<p>Option 3</p>')],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function countryCodes(): array
    {
        return [
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
        ];
    }

    /**
     * @return list<string>
     */
    private function allowedMimes(): array
    {
        return [
            '.jpg',
            '.png',
            '.gif',
            '.webp',
            '.svg',
            '.pdf',
            '.doc',
            '.docx',
            '.xls',
            '.xlsx',
            '.zip',
            '.mp4',
        ];
    }

    private function displayName(string $type): string
    {
        return collect(explode('_', $type))
            ->map(fn (string $part) => ucfirst($part))
            ->implode(' ');
    }
}
