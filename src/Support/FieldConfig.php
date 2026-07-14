<?php

namespace Teamnovu\Formbuilder\Support;

class FieldConfig
{
    public static function label(): array
    {
        return [
            'display' => __('formbuilder::form.label.display'),
            'instructions' => __('formbuilder::form.label.instruction'),
            'type' => 'translatable_input',
        ];
    }

    public static function help(): array
    {
        return [
            'display' => __('formbuilder::form.help.display'),
            'instructions' => __('formbuilder::form.help.instruction'),
            'type' => 'translatable_input',
        ];
    }

    public static function hint(): array
    {
        return [
            'display' => __('formbuilder::form.hint.display'),
            'instructions' => __('formbuilder::form.hint.instruction'),
            'type' => 'translatable_input',
        ];
    }

    public static function placeholder(): array
    {
        return [
            'display' => __('formbuilder::form.placeholder.display'),
            'instructions' => __('formbuilder::form.placeholder.instruction'),
            'type' => 'translatable_input',
        ];
    }

    public static function floatingLabel(): array
    {
        return [
            'display' => __('formbuilder::form.floating_label.display'),
            'instructions' => __('formbuilder::form.floating_label.instruction'),
            'type' => 'toggle',
            'default' => (bool) config('formbuilder.floating_label', false),
            'force_in_config' => true,
            'visibility' => 'hidden',
        ];
    }

    public static function characterLimit(): array
    {
        return [
            'display' => __('formbuilder::form.character_limit.display'),
            'instructions' => __('formbuilder::form.character_limit.instruction'),
            'type' => 'integer',
        ];
    }

    public static function orientation(string $default = 'horizontal'): array
    {
        return [
            'display' => __('formbuilder::form.orientation.display'),
            'instructions' => __('formbuilder::form.orientation.instruction'),
            'type' => 'select',
            'options' => [
                'horizontal' => __('formbuilder::form.orientation.horizontal'),
                'vertical' => __('formbuilder::form.orientation.vertical'),
            ],
            'default' => $default,
            'force_in_config' => true,
        ];
    }

    public static function variant(string $default = 'list'): array
    {
        return [
            'display' => __('formbuilder::form.variant.display'),
            'instructions' => __('formbuilder::form.variant.instruction'),
            'type' => 'select',
            'options' => [
                'card' => __('formbuilder::form.variant.card'),
                'list' => __('formbuilder::form.variant.list'),
                'table' => __('formbuilder::form.variant.table'),
            ],
            'default' => $default,
            'force_in_config' => true,
        ];
    }

    public static function indicator(string $default = 'start'): array
    {
        return [
            'display' => __('formbuilder::form.indicator.display'),
            'instructions' => __('formbuilder::form.indicator.instruction'),
            'type' => 'select',
            'options' => [
                'start' => __('formbuilder::form.indicator.start'),
                'end' => __('formbuilder::form.indicator.end'),
                'hidden' => __('formbuilder::form.indicator.hidden'),
            ],
            'default' => $default,
            'force_in_config' => true,
        ];
    }

    public static function options(string $textType = 'translatable_bard'): array
    {
        return [
            'display' => __('formbuilder::form.options.display'),
            'instructions' => __('formbuilder::form.options.instruction'),
            'type' => 'grid',
            'fields' => [
                ['handle' => 'key', 'field' => ['type' => 'text']],
                ['handle' => 'text', 'field' => ['type' => $textType]],
            ],
        ];
    }

    public static function earliestDate(): array
    {
        return [
            'display' => __('formbuilder::form.earliest_date.display'),
            'instructions' => __('formbuilder::form.earliest_date.instruction'),
            'type' => 'date',
        ];
    }

    public static function latestDate(): array
    {
        return [
            'display' => __('formbuilder::form.latest_date.display'),
            'instructions' => __('formbuilder::form.latest_date.instruction'),
            'type' => 'date',
        ];
    }

    public static function min(int $default = 0): array
    {
        return [
            'display' => __('formbuilder::form.min.display'),
            'instructions' => __('formbuilder::form.min.instruction'),
            'type' => 'integer',
            'default' => $default,
        ];
    }

    public static function max(int $default = 100): array
    {
        return [
            'display' => __('formbuilder::form.max.display'),
            'instructions' => __('formbuilder::form.max.instruction'),
            'type' => 'integer',
            'default' => $default,
        ];
    }

    public static function integerDefault(): array
    {
        return [
            'display' => __('formbuilder::form.default.display'),
            'instructions' => __('formbuilder::form.default.instruction'),
            'type' => 'integer',
        ];
    }

    public static function step(int $default = 1): array
    {
        return [
            'display' => __('formbuilder::form.step.display'),
            'instructions' => __('formbuilder::form.step.instruction'),
            'type' => 'integer',
            'default' => $default,
        ];
    }

    /**
     * Shared label / optional placeholder / help / hint fields used by almost every input.
     *
     * Help and hint are included when `formbuilder.show_help` / `formbuilder.show_hint`
     * are enabled (default true).
     *
     * @return array<string, array>
     */
    private static function baseFields(bool $placeholder = false): array
    {
        $fields = [
            'label' => self::label(),
        ];

        if ($placeholder) {
            $fields['placeholder'] = self::placeholder();
        }

        if ((bool) config('formbuilder.show_help', true)) {
            $fields['help'] = self::help();
        }

        if ((bool) config('formbuilder.show_hint', true)) {
            $fields['hint'] = self::hint();
        }

        return $fields;
    }

    /**
     * Build the standard Input Behavior section, always including base fields.
     *
     * @param  array<string, array>  $fields
     * @return list<array{display: string, fields: array<string, array>}>
     */
    public static function createItems(array $fields = [], bool $placeholder = false): array
    {
        return [self::section(__('Input Behavior'), [
            ...self::baseFields($placeholder),
            ...$fields,
        ])];
    }

    /**
     * @param  array<string, array>  $fields
     * @return array{display: string, fields: array<string, array>}
     */
    public static function section(string $display, array $fields): array
    {
        return [
            'display' => $display,
            'fields' => $fields,
        ];
    }
}
