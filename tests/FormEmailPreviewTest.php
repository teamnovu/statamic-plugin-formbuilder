<?php

namespace Teamnovu\Formbuilder\Tests;

use Teamnovu\Formbuilder\Tests\Concerns\SavesForms;

class FormEmailPreviewTest extends TestCase
{
    use SavesForms;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsSuperUser();

        $this->saveForm('email_preview_test', [
            'vorname' => ['type' => 'input_text', 'display' => 'Vorname'],
            'trenner' => ['type' => 'spacer', 'display' => 'Trenner'],
            'nachname' => ['type' => 'input_text', 'display' => 'Nachname'],
            'nachricht' => ['type' => 'input_textarea', 'display' => 'Nachricht'],
        ], [
            ['to' => 'a@example.test', 'html' => 'formbuilder::emails.submission', 'markdown' => true],
            [
                'to' => 'b@example.test',
                'html' => 'formbuilder::emails.submission',
                'markdown' => true,
                'custom_field_selection' => true,
                'included_fields' => ['nachricht', 'vorname'],
            ],
        ]);
    }

    public function test_preview_lists_all_fields_without_spacers_when_the_selection_is_off(): void
    {
        $html = $this->preview(0);

        $this->assertStringNotContainsString('[Trenner]', $html);
        $this->assertLessThan(strpos($html, '[Nachname]'), strpos($html, '[Vorname]'));
        $this->assertLessThan(strpos($html, '[Nachricht]'), strpos($html, '[Nachname]'));
    }

    public function test_preview_lists_only_the_selected_fields_in_order(): void
    {
        $html = $this->preview(1);

        $this->assertStringNotContainsString('[Nachname]', $html);
        $this->assertNotFalse(strpos($html, '[Vorname]'));
        $this->assertLessThan(strpos($html, '[Vorname]'), strpos($html, '[Nachricht]'));
    }

    private function preview(int $index): string
    {
        return $this->get(cp_route('forms.email-preview', 'email_preview_test').'?email_index='.$index.'&locale=default')
            ->assertOk()
            ->getContent();
    }
}
