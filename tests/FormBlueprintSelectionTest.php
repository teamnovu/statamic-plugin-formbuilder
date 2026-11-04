<?php

namespace Teamnovu\Formbuilder\Tests;

use ReflectionMethod;
use Statamic\Facades\Form;
use Statamic\Http\Controllers\CP\Forms\FormBlueprintController as StatamicFormBlueprintController;
use Teamnovu\Formbuilder\Tests\Concerns\SavesForms;

class FormBlueprintSelectionTest extends TestCase
{
    use SavesForms;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsSuperUser();

        $this->saveForm('blueprint_selection_test', [
            'vorname' => ['type' => 'input_text'],
            'nachname' => ['type' => 'input_text'],
            'email' => ['type' => 'input_email'],
            'nachricht' => ['type' => 'input_textarea'],
        ], [
            ['to' => 'a@example.test', 'custom_field_selection' => true, 'included_fields' => ['nachricht', 'vorname', 'nachname']],
            ['to' => 'b@example.test', 'custom_field_selection' => true, 'included_fields' => ['email', 'vorname']],
            ['to' => 'c@example.test'],
        ]);
    }

    public function test_renamed_fields_keep_their_position_and_deleted_fields_are_removed(): void
    {
        $payload = $this->editorPayload();
        $payload = $this->renameField($payload, 'vorname', 'first_name');
        $payload = $this->deleteField($payload, 'nachname');

        $this->saveBlueprint($payload);

        $this->assertSame([
            ['nachricht', 'first_name'],
            ['email', 'first_name'],
            null,
        ], $this->selections());
    }

    public function test_a_field_can_be_renamed_to_the_handle_of_a_field_deleted_in_the_same_save(): void
    {
        $this->saveForm('blueprint_selection_test', [
            'first_name' => ['type' => 'input_text'],
            'name' => ['type' => 'input_text'],
            'email' => ['type' => 'input_email'],
        ], [
            ['to' => 'a@example.test', 'custom_field_selection' => true, 'included_fields' => ['email', 'first_name']],
            ['to' => 'b@example.test', 'custom_field_selection' => true, 'included_fields' => ['name', 'email']],
        ]);

        $payload = $this->editorPayload();
        $payload = $this->deleteField($payload, 'name');
        $payload = $this->renameField($payload, 'first_name', 'name');

        $this->saveBlueprint($payload);

        $this->assertSame([
            ['email', 'name'],
            ['email'],
        ], $this->selections());
    }

    public function test_a_field_can_take_over_the_old_handle_of_another_renamed_field(): void
    {
        $this->saveForm('blueprint_selection_test', [
            'a' => ['type' => 'input_text'],
            'b' => ['type' => 'input_text'],
        ], [
            ['to' => 'a@example.test', 'custom_field_selection' => true, 'included_fields' => ['b', 'a']],
            ['to' => 'b@example.test', 'custom_field_selection' => true, 'included_fields' => ['a']],
        ]);

        $payload = $this->editorPayload();
        $payload = $this->renameField($payload, 'b', 'c');
        $payload = $this->renameField($payload, 'a', 'b');

        $this->saveBlueprint($payload);

        $this->assertSame([
            ['c', 'b'],
            ['b'],
        ], $this->selections());
    }

    public function test_added_fields_are_not_added_to_a_selection(): void
    {
        $payload = $this->editorPayload();
        $payload['tabs'][0]['sections'][0]['fields'][] = $this->newField('telefon');

        $this->saveBlueprint($payload);

        $this->assertSame([
            ['nachricht', 'vorname', 'nachname'],
            ['email', 'vorname'],
            null,
        ], $this->selections());
    }

    public function test_a_second_save_from_the_same_page_follows_renames_after_a_reorder(): void
    {
        // The editor keeps its load-time ids after saving, so they no longer match field positions.
        $payload = $this->editorPayload();
        $fields = &$payload['tabs'][0]['sections'][0]['fields'];
        $fields = array_reverse($fields);
        unset($fields);

        $this->saveBlueprint($payload);

        $payload = $this->renameField($payload, 'vorname', 'first_name');
        $payload = $this->renameField($payload, 'nachricht', 'message');

        $this->saveBlueprint($payload);

        $this->assertSame([
            ['message', 'first_name', 'nachname'],
            ['email', 'first_name'],
            null,
        ], $this->selections());
    }

    public function test_renames_are_not_guessed_when_the_ids_do_not_match_the_saved_blueprint(): void
    {
        // A page loaded before the blueprint was reordered elsewhere, without a session map.
        $payload = $this->editorPayload();
        $fields = $payload['tabs'][0]['sections'][0]['fields'];
        $this->saveBlueprint(array_replace_recursive($payload, ['tabs' => [0 => ['sections' => [0 => ['fields' => array_reverse($fields)]]]]]));
        session()->flush();

        // By position, these would read as nachricht → first_name and vorname → message.
        $payload = $this->renameField($payload, 'vorname', 'first_name');
        $payload = $this->renameField($payload, 'nachricht', 'message');

        $this->saveBlueprint($payload);

        $this->assertSame([
            ['nachname'],
            ['email'],
            null,
        ], $this->selections());
    }

    public function test_opening_the_editor_in_a_second_tab_does_not_break_renames_of_the_first(): void
    {
        $first = $this->editorPayload('tabone');
        $fields = &$first['tabs'][0]['sections'][0]['fields'];
        $fields = array_reverse($fields);
        unset($fields);

        $this->saveBlueprint($first);

        // Loading the editor again must leave the map of the first tab alone.
        $this->get(cp_route('blueprints.forms.edit', 'blueprint_selection_test'))->assertOk();

        $first = $this->renameField($first, 'vorname', 'first_name');

        $this->saveBlueprint($first);

        $this->assertSame([
            ['nachricht', 'first_name', 'nachname'],
            ['email', 'first_name'],
            null,
        ], $this->selections());
    }

    public function test_the_edit_page_hands_the_editor_a_token(): void
    {
        $this->get(cp_route('blueprints.forms.edit', 'blueprint_selection_test'))
            ->assertInertia(fn ($page) => $page->has('blueprint.formbuilder_editor'));
    }

    private function editorPayload(string $editor = 'editorone'): array
    {
        $controller = app(StatamicFormBlueprintController::class);
        $method = new ReflectionMethod(StatamicFormBlueprintController::class, 'toVueObject');

        return $method->invoke($controller, Form::find('blueprint_selection_test')->blueprint())
            + ['formbuilder_editor' => $editor];
    }

    private function renameField(array $payload, string $from, string $to): array
    {
        foreach ($payload['tabs'][0]['sections'][0]['fields'] as &$field) {
            if ($field['handle'] === $from) {
                $field['handle'] = $to;
            }
        }

        return $payload;
    }

    private function deleteField(array $payload, string $handle): array
    {
        $payload['tabs'][0]['sections'][0]['fields'] = array_values(array_filter(
            $payload['tabs'][0]['sections'][0]['fields'],
            fn (array $field) => $field['handle'] !== $handle
        ));

        return $payload;
    }

    private function newField(string $handle): array
    {
        return [
            '_id' => 'V1StGXR8_Z5jdHi6B-myT',
            'handle' => $handle,
            'type' => 'inline',
            'fieldtype' => 'input_text',
            'config' => ['type' => 'input_text', 'display' => ucfirst($handle)],
        ];
    }

    private function saveBlueprint(array $payload): void
    {
        $this->patchJson(cp_route('blueprints.forms.update', 'blueprint_selection_test'), $payload)->assertOk();
    }

    private function selections(): array
    {
        return collect(Form::find('blueprint_selection_test')->email())
            ->map(fn (array $email) => $email['included_fields'] ?? null)
            ->all();
    }
}
