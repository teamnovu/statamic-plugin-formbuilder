<?php

namespace Teamnovu\Formbuilder\Tests;

use ReflectionMethod;
use Teamnovu\Formbuilder\Fieldtypes\InputCheckboxes;
use Teamnovu\Formbuilder\Fieldtypes\InputDate;
use Teamnovu\Formbuilder\Fieldtypes\InputDaterange;
use Teamnovu\Formbuilder\Fieldtypes\InputEmail;
use Teamnovu\Formbuilder\Fieldtypes\InputFileUpload;
use Teamnovu\Formbuilder\Fieldtypes\InputNumber;
use Teamnovu\Formbuilder\Fieldtypes\InputPhone;
use Teamnovu\Formbuilder\Fieldtypes\InputRadioButtons;
use Teamnovu\Formbuilder\Fieldtypes\InputSelect;
use Teamnovu\Formbuilder\Fieldtypes\InputSlider;
use Teamnovu\Formbuilder\Fieldtypes\InputSwitch;
use Teamnovu\Formbuilder\Fieldtypes\InputText;
use Teamnovu\Formbuilder\Fieldtypes\InputTextarea;
use Teamnovu\Formbuilder\Support\FieldConfig;

class FieldConfigTest extends TestCase
{
    public function test_create_items_always_includes_base_fields(): void
    {
        $items = FieldConfig::createItems();

        $this->assertCount(1, $items);
        $this->assertSame(__('Input Behavior'), $items[0]['display']);
        $this->assertSame(['label', 'hide_label', 'help', 'hint'], array_keys($items[0]['fields']));
    }

    public function test_create_items_can_include_placeholder(): void
    {
        $items = FieldConfig::createItems(placeholder: true);

        $this->assertSame(['label', 'hide_label', 'placeholder', 'help', 'hint'], array_keys($items[0]['fields']));
        $this->assertSame('translatable_input', $items[0]['fields']['placeholder']['type']);
    }

    public function test_create_items_appends_extra_fields_after_base_fields(): void
    {
        $items = FieldConfig::createItems([
            'floating_label' => FieldConfig::floatingLabel(),
        ], placeholder: true);

        $this->assertSame(
            ['label', 'hide_label', 'placeholder', 'help', 'hint', 'floating_label'],
            array_keys($items[0]['fields'])
        );
    }

    public function test_create_items_omits_help_when_disabled(): void
    {
        config(['formbuilder.show_help' => false]);

        $items = FieldConfig::createItems(placeholder: true);

        $this->assertSame(['label', 'hide_label', 'placeholder', 'hint'], array_keys($items[0]['fields']));
    }

    public function test_create_items_omits_hint_when_disabled(): void
    {
        config(['formbuilder.show_hint' => false]);

        $items = FieldConfig::createItems(placeholder: true);

        $this->assertSame(['label', 'hide_label', 'placeholder', 'help'], array_keys($items[0]['fields']));
    }

    public function test_create_items_omits_help_and_hint_when_both_disabled(): void
    {
        config([
            'formbuilder.show_help' => false,
            'formbuilder.show_hint' => false,
        ]);

        $items = FieldConfig::createItems();

        $this->assertSame(['label', 'hide_label'], array_keys($items[0]['fields']));
    }

    public function test_date_fieldtype_uses_create_items_without_extra_fields(): void
    {
        $fields = $this->configFields(InputDate::class);

        $this->assertSame(['label', 'hide_label', 'help', 'hint'], array_keys($fields));
    }

    public function test_hide_label_is_toggle_defaulting_false(): void
    {
        $this->assertSame([
            'type' => 'toggle',
            'default' => false,
        ], $this->fieldConfig(InputCheckboxes::class, 'hide_label'));
    }

    public function test_floating_label_is_hidden_and_defaults_from_global_config(): void
    {
        config(['formbuilder.floating_label' => false]);

        foreach ([InputText::class, InputEmail::class, InputTextarea::class, InputSelect::class] as $fieldtype) {
            $this->assertSame([
                'type' => 'toggle',
                'default' => false,
                'force_in_config' => true,
                'visibility' => 'hidden',
            ], $this->fieldConfig($fieldtype, 'floating_label'));
        }
    }

    public function test_floating_label_propagates_enabled_global_config(): void
    {
        config(['formbuilder.floating_label' => true]);

        foreach ([InputText::class, InputEmail::class, InputTextarea::class, InputSelect::class] as $fieldtype) {
            $this->assertSame([
                'type' => 'toggle',
                'default' => true,
                'force_in_config' => true,
                'visibility' => 'hidden',
            ], $this->fieldConfig($fieldtype, 'floating_label'));
        }
    }

    public function test_choice_fieldtypes_share_orientation_variant_indicator_and_options(): void
    {
        foreach ([InputCheckboxes::class, InputRadioButtons::class] as $fieldtype) {
            $fields = $this->configFields($fieldtype);

            $this->assertSame(FieldConfig::orientation(), $fields['orientation']);
            $this->assertSame(FieldConfig::variant(), $fields['variant']);
            $this->assertSame(FieldConfig::indicator(), $fields['indicator']);
            $this->assertSame(FieldConfig::options(), $fields['options']);
        }
    }

    public function test_select_options_use_translatable_input(): void
    {
        $fields = $this->configFields(InputSelect::class);

        $this->assertSame(
            FieldConfig::options(textType: 'translatable_input'),
            $fields['options']
        );
    }

    public function test_date_boundary_fields_are_date_only(): void
    {
        $this->assertSame([
            'type' => 'date',
            'time_enabled' => false,
            'format' => 'Y-m-d',
        ], collect(FieldConfig::earliestDate())->only(['type', 'time_enabled', 'format'])->all());

        $this->assertSame([
            'type' => 'date',
            'time_enabled' => false,
            'format' => 'Y-m-d',
        ], collect(FieldConfig::latestDate())->only(['type', 'time_enabled', 'format'])->all());
    }

    public function test_text_like_fieldtypes_expose_autocomplete_select_last(): void
    {
        foreach ([InputText::class, InputEmail::class, InputTextarea::class, InputPhone::class, InputNumber::class] as $fieldtype) {
            $fields = $this->configFields($fieldtype);

            $this->assertSame('autocomplete', array_key_last($fields), $fieldtype);

            $autocomplete = $fields['autocomplete'];

            $this->assertSame('select', $autocomplete['type']);
            $this->assertTrue($autocomplete['searchable']);
            $this->assertTrue($autocomplete['clearable']);
            $this->assertArrayNotHasKey('default', $autocomplete);
            $this->assertArrayNotHasKey('force_in_config', $autocomplete);
            $this->assertArrayHasKey('email', $autocomplete['options']);
            $this->assertArrayHasKey('off', $autocomplete['options']);
            $this->assertArrayNotHasKey('on', $autocomplete['options']);
        }
    }

    public function test_non_text_fieldtypes_do_not_expose_autocomplete(): void
    {
        foreach ([
            InputDate::class,
            InputDaterange::class,
            InputSelect::class,
            InputSlider::class,
            InputSwitch::class,
            InputRadioButtons::class,
            InputCheckboxes::class,
            InputFileUpload::class,
        ] as $fieldtype) {
            $this->assertArrayNotHasKey('autocomplete', $this->configFields($fieldtype), $fieldtype);
        }
    }

    public function test_autocomplete_options_start_with_off_and_hold_55_tokens(): void
    {
        $tokens = array_keys(FieldConfig::autocomplete()['options']);

        $this->assertCount(55, $tokens);
        $this->assertSame('off', $tokens[0]);
        $this->assertContains('one-time-code', $tokens);
        $this->assertContains('impp', $tokens);
    }

    public function test_autocomplete_option_labels_are_translated_in_german_and_english(): void
    {
        foreach (['de' => 'Vorname (given-name)', 'en' => 'First name (given-name)'] as $locale => $givenName) {
            app()->setLocale($locale);

            $options = FieldConfig::autocomplete()['options'];

            $this->assertSame($givenName, $options['given-name']);

            foreach ($options as $token => $label) {
                $key = "formbuilder::form.autocomplete.options.{$token}";

                $this->assertTrue(trans()->hasForLocale($key, $locale), "Missing {$locale} translation for {$token}");
                $this->assertSame(__($key)." ({$token})", $label);
                $this->assertStringNotContainsString('formbuilder::', $label);
            }
        }
    }

    /**
     * @return array<string, array>
     */
    private function configFields(string $fieldtypeClass): array
    {
        $method = new ReflectionMethod($fieldtypeClass, 'configFieldItems');
        $items = $method->invoke(new $fieldtypeClass);

        return $items[0]['fields'];
    }

    /**
     * @return array<string, mixed>
     */
    private function fieldConfig(string $fieldtypeClass, string $handle): array
    {
        return collect($this->configFields($fieldtypeClass)[$handle])
            ->only(['type', 'default', 'force_in_config', 'visibility'])
            ->all();
    }
}
