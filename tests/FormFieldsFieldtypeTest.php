<?php

namespace Teamnovu\Formbuilder\Tests;

use Statamic\Facades\User;
use Teamnovu\Formbuilder\Tests\Concerns\SavesForms;

class FormFieldsFieldtypeTest extends TestCase
{
    use SavesForms;

    protected function setUp(): void
    {
        parent::setUp();

        $this->saveForm('form_fields_test', [
            'vorname' => ['type' => 'input_text', 'display' => 'Vorname'],
            'trenner' => ['type' => 'spacer'],
            'hinweis' => ['type' => 'display_text'],
            'email' => ['type' => 'input_email', 'display' => 'E-Mail'],
            'nachricht' => ['type' => 'input_textarea', 'display' => 'Nachricht'],
        ]);
    }

    public function test_index_items_are_the_form_fields_in_blueprint_order_without_valueless_fields(): void
    {
        $this->actingAsSuperUser();
        app()->setLocale('en');

        $response = $this->getJson($this->indexUrl())->assertOk();

        $this->assertSame(['vorname', 'email', 'nachricht'], array_column($response->json('data'), 'id'));
        $this->assertSame([
            'id' => 'vorname',
            'title' => 'Vorname',
            'handle' => 'vorname',
            'type' => 'Form Input Text',
            'hint' => 'vorname',
        ], $response->json('data.0'));
        $this->assertSame(['title', 'handle', 'type'], array_column($response->json('meta.columns'), 'field'));
    }

    public function test_search_filters_the_index_items(): void
    {
        $this->actingAsSuperUser();

        $byDisplay = $this->getJson($this->indexUrl(['search' => 'nach']))->assertOk();
        $byHandle = $this->getJson($this->indexUrl(['search' => 'EMAIL']))->assertOk();

        $this->assertSame(['nachricht'], array_column($byDisplay->json('data'), 'id'));
        $this->assertSame(['email'], array_column($byHandle->json('data'), 'id'));
    }

    public function test_a_handle_missing_from_the_blueprint_is_an_invalid_item(): void
    {
        $this->actingAsSuperUser();

        $response = $this->postJson(cp_route('relationship.data'), [
            'config' => $this->encodedConfig(),
            'selections' => ['nachricht', 'geloescht'],
        ])->assertOk();

        $this->assertSame('Nachricht', $response->json('data.0.title'));
        $this->assertArrayNotHasKey('invalid', $response->json('data.0'));
        $this->assertSame(['id' => 'geloescht', 'title' => 'geloescht', 'invalid' => true], $response->json('data.1'));
    }

    public function test_index_items_require_the_configure_forms_permission(): void
    {
        $this->actingAs(User::make()->id('form-fields-no-permission')->email('viewer@example.com'));

        $this->getJson($this->indexUrl())->assertForbidden();
    }

    private function indexUrl(array $query = []): string
    {
        return cp_route('relationship.index', array_merge(['config' => $this->encodedConfig()], $query));
    }

    private function encodedConfig(): string
    {
        return base64_encode(json_encode(['type' => 'form_fields', 'form' => 'form_fields_test']));
    }
}
