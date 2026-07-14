<?php

namespace Teamnovu\Formbuilder\Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Teamnovu\Formbuilder\Support\ExampleFormBlueprint;

class ExampleFormBlueprintTest extends TestCase
{
    /** @var list<string> */
    private const PUBLIC_FIELDTYPES = [
        'input_text',
        'input_textarea',
        'input_email',
        'input_phone',
        'input_number',
        'input_slider',
        'input_select',
        'input_date',
        'input_daterange',
        'input_switch',
        'input_radio_buttons',
        'input_checkboxes',
        'input_file_upload',
        'display_text',
    ];

    public function test_it_localizes_values_for_every_configured_site(): void
    {
        $blueprint = new ExampleFormBlueprint(['de', 'en', 'it']);

        $localized = $blueprint->localized('Placeholder');

        $this->assertSame([
            ['handle' => 'de', 'value' => 'Placeholder'],
            ['handle' => 'en', 'value' => 'Placeholder en'],
            ['handle' => 'it', 'value' => 'Placeholder it'],
        ], $localized);
    }

    public function test_it_keeps_the_base_value_for_a_single_site(): void
    {
        $blueprint = new ExampleFormBlueprint(['de']);

        $this->assertSame([
            ['handle' => 'de', 'value' => 'Hint'],
        ], $blueprint->localized('Hint'));
    }

    public function test_it_includes_every_public_fieldtype(): void
    {
        $blueprint = new ExampleFormBlueprint(['de', 'fr']);
        $types = collect($blueprint->toArray()['tabs']['main']['sections'][0]['fields'])
            ->pluck('field.type')
            ->unique()
            ->values()
            ->all();

        foreach (self::PUBLIC_FIELDTYPES as $type) {
            $this->assertContains($type, $types, "Missing fieldtype: {$type}");
        }

        $this->assertContains('spacer', $types);
    }

    public function test_it_applies_site_handles_to_field_labels(): void
    {
        $fields = (new ExampleFormBlueprint(['de', 'en']))->toArray()['tabs']['main']['sections'][0]['fields'];
        $textField = collect($fields)->firstWhere('handle', 'input_text');

        $this->assertSame([
            ['handle' => 'de', 'value' => 'Input Text'],
            ['handle' => 'en', 'value' => 'Input Text en'],
        ], $textField['field']['label']);
    }

    public function test_it_uses_varied_field_widths(): void
    {
        $fields = collect((new ExampleFormBlueprint(['de']))->toArray()['tabs']['main']['sections'][0]['fields'])
            ->keyBy('handle');

        $this->assertSame(50, $fields['input_text']['field']['width']);
        $this->assertSame(33, $fields['input_phone']['field']['width']);
        $this->assertSame(33, $fields['input_phone_country_code']['field']['width']);
        $this->assertSame(33, $fields['input_phone_country_code_search']['field']['width']);
        $this->assertSame(33, $fields['input_number']['field']['width']);
        $this->assertSame(66, $fields['input_slider']['field']['width']);
        $this->assertArrayNotHasKey('width', $fields['input_textarea']['field']);
    }

    public function test_publish_command_writes_form_and_blueprint(): void
    {
        $blueprintPath = rtrim(config('statamic.system.blueprints_path'), '/').'/forms/template.yaml';
        $formPath = \Statamic\Facades\Form::make('template')->path();

        File::delete([$blueprintPath, $formPath]);

        $this->assertSame(0, Artisan::call('formbuilder:publish-example-form'));
        $this->assertFileExists($blueprintPath);
        $this->assertFileExists($formPath);
        $this->assertStringContainsString('type: input_text', File::get($blueprintPath));
        $this->assertStringContainsString('title: Template', File::get($formPath));
    }

    public function test_publish_command_refuses_to_overwrite_without_force(): void
    {
        $blueprintPath = rtrim(config('statamic.system.blueprints_path'), '/').'/forms/template.yaml';
        $formPath = \Statamic\Facades\Form::make('template')->path();

        File::ensureDirectoryExists(dirname($blueprintPath));
        File::ensureDirectoryExists(dirname($formPath));
        File::put($blueprintPath, "tabs: {}\n");
        File::put($formPath, "title: Existing\n");

        $this->assertSame(1, Artisan::call('formbuilder:publish-example-form'));
        $this->assertSame("tabs: {}\n", File::get($blueprintPath));

        $this->assertSame(0, Artisan::call('formbuilder:publish-example-form', ['--force' => true]));
        $this->assertStringContainsString('type: input_text', File::get($blueprintPath));
        $this->assertStringContainsString('title: Template', File::get($formPath));
    }

    public function test_publish_command_respects_configured_forms_path(): void
    {
        $customFormsPath = storage_path('formbuilder-test-forms');
        $customBlueprintsPath = storage_path('formbuilder-test-blueprints');

        config([
            'statamic.forms.forms' => $customFormsPath,
            'statamic.system.blueprints_path' => $customBlueprintsPath,
        ]);

        $blueprintPath = $customBlueprintsPath.'/forms/template.yaml';
        $formPath = $customFormsPath.'/template.yaml';

        File::deleteDirectory($customFormsPath);
        File::deleteDirectory($customBlueprintsPath);

        $this->assertSame(0, Artisan::call('formbuilder:publish-example-form'));
        $this->assertFileExists($blueprintPath);
        $this->assertFileExists($formPath);

        File::deleteDirectory($customFormsPath);
        File::deleteDirectory($customBlueprintsPath);
    }
}
