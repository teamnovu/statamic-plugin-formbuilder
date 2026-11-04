<?php

namespace Teamnovu\Formbuilder\Tests;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Statamic\Facades\Blueprint;
use Statamic\Facades\Form;
use Statamic\Facades\Site;
use Statamic\Forms\Submission;
use Symfony\Component\Mime\Email;
use Teamnovu\Formbuilder\Mail\FormEmail;

class FormEmailRenderingTest extends TestCase
{
    #[DataProvider('participantCounts')]
    public function test_internal_email_includes_submitted_counts_despite_blueprint_defaults(int|float $children, int $adults): void
    {
        $bodies = $this->sendEmail([
            'children' => ['type' => 'input_number', 'display' => 'Children', 'default' => 5],
            'adults' => ['type' => 'input_number', 'display' => 'Adults', 'default' => 1],
        ], ['children' => $children, 'adults' => $adults]);

        foreach ($bodies as $body) {
            $this->assertMatchesRegularExpression('/Children\s+'.preg_quote((string) $children, '/').'(?=\s|$)/', $body);
            $this->assertMatchesRegularExpression('/Adults\s+'.$adults.'(?=\s|$)/', $body);
        }
    }

    public static function participantCounts(): array
    {
        return [[5, 1], [8, 2], [0, 0], [2.5, 1]];
    }

    public function test_internal_email_includes_string_checkbox_values_and_existing_option_labels(): void
    {
        $bodies = $this->sendEmail([
            'agb' => ['type' => 'input_checkboxes', 'display' => 'Terms', 'options' => [['key' => 'einverstanden']]],
            'newsletter' => ['type' => 'input_checkboxes', 'display' => 'Newsletter', 'options' => [['key' => 'Ja']]],
            'choices' => ['type' => 'input_checkboxes', 'display' => 'Choices', 'options' => [['key' => 'a'], ['key' => 'b']]],
        ], [
            'agb' => ['einverstanden'],
            'newsletter' => ['Ja'],
            'choices' => [['value' => 'a', 'label' => 'First option'], ['value' => 'b']],
        ]);

        foreach ($bodies as $body) {
            $this->assertMatchesRegularExpression('/Terms\s+(?:-\s+)?einverstanden/', $body);
            $this->assertMatchesRegularExpression('/Newsletter\s+(?:-\s+)?Ja/', $body);
            $this->assertMatchesRegularExpression('/First option\s+(?:-\s+)?b\b/', $body);
        }
    }

    public function test_internal_email_omits_empty_optional_checkboxes(): void
    {
        $bodies = $this->sendEmail([
            'agb' => ['type' => 'input_checkboxes', 'display' => 'Terms', 'options' => [['key' => 'einverstanden']]],
            'newsletter' => ['type' => 'input_checkboxes', 'display' => 'Newsletter', 'options' => [['key' => 'Ja']]],
        ], ['agb' => ['einverstanden'], 'newsletter' => []]);

        foreach ($bodies as $body) {
            $this->assertStringContainsString('einverstanden', $body);
            $this->assertStringNotContainsString('Newsletter', $body);
        }
    }

    public function test_custom_email_uses_the_same_checkbox_states_in_html_and_readable_plain_text(): void
    {
        foreach ([['Ja'], []] as $newsletter) {
            $bodies = $this->sendEmail([
                'agb' => ['type' => 'input_checkboxes'],
                'newsletter' => ['type' => 'input_checkboxes'],
            ], ['agb' => ['einverstanden'], 'newsletter' => $newsletter], [
                'html' => 'formbuilder::emails.user-submission',
                'text' => 'formbuilder::emails.user-submission-text',
                'mail_text' => '<p>Terms: {{ if agb }}accepted{{ /if }}<br>Newsletter: {{ if newsletter }}subscribed{{ else }}not subscribed{{ /if }}</p>',
            ]);

            foreach ($bodies as $body) {
                $this->assertStringContainsString('Terms: accepted Newsletter: '.($newsletter ? 'subscribed' : 'not subscribed'), $body);
            }
        }
    }

    public function test_email_lists_all_fields_when_the_selection_is_off(): void
    {
        $bodies = $this->sendEmail(self::CONTACT_FIELDS, self::CONTACT_DATA, [
            'custom_field_selection' => false,
            'included_fields' => ['message'],
        ]);

        foreach ($bodies as $body) {
            $this->assertFieldOrder(['First name', 'Address', 'E-Mail', 'Message'], $body);
        }
    }

    public function test_email_lists_only_the_selected_fields_in_the_selected_order(): void
    {
        $bodies = $this->sendEmail(self::CONTACT_FIELDS, self::CONTACT_DATA, [
            'custom_field_selection' => true,
            'included_fields' => ['address', 'first_name'],
        ]);

        foreach ($bodies as $body) {
            $this->assertFieldOrder(['Address', 'First name'], $body);
            $this->assertStringNotContainsString('Message', $body);
            $this->assertStringNotContainsString('Hello there', $body);
            $this->assertStringNotContainsString('E-Mail', $body);
        }
    }

    public function test_email_lists_no_fields_when_the_selection_is_on_and_empty(): void
    {
        $bodies = $this->sendEmail(self::CONTACT_FIELDS, self::CONTACT_DATA, [
            'custom_field_selection' => true,
            'included_fields' => null,
        ]);

        foreach ($bodies as $body) {
            foreach (['First name', 'Address', 'E-Mail', 'Message'] as $display) {
                $this->assertStringNotContainsString($display, $body);
            }
        }
    }

    public function test_a_selected_handle_missing_from_the_blueprint_is_skipped_and_logged(): void
    {
        Log::spy();

        $bodies = $this->sendEmail(self::CONTACT_FIELDS, self::CONTACT_DATA, [
            'custom_field_selection' => true,
            'included_fields' => ['message', 'deleted_field', 'first_name'],
        ]);

        foreach ($bodies as $body) {
            $this->assertFieldOrder(['Message', 'First name'], $body);
        }

        Log::shouldHaveReceived('warning')
            ->withArgs(fn (string $message, array $context) => $context['missing'] === ['deleted_field']);
    }

    public function test_placeholders_can_use_fields_that_are_not_selected(): void
    {
        $sent = $this->send(self::CONTACT_FIELDS, self::CONTACT_DATA, [
            'to' => '{{ email }}',
            'subject' => 'Message from {{ first_name }}',
            'custom_field_selection' => true,
            'included_fields' => ['message'],
        ]);

        $this->assertSame('anna@example.test', $sent->getTo()[0]->getAddress());
        $this->assertSame('Message from Anna', $sent->getSubject());
    }

    public function test_only_selected_upload_fields_are_attached(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('statamic/file-uploads/cv.pdf', 'cv');
        Storage::disk('local')->put('statamic/file-uploads/photo.jpg', 'photo');

        $sent = $this->send([
            'first_name' => ['type' => 'text', 'display' => 'First name'],
            'cv' => ['type' => 'files', 'display' => 'CV'],
            'photo' => ['type' => 'files', 'display' => 'Photo'],
        ], ['first_name' => 'Anna', 'cv' => ['cv.pdf'], 'photo' => ['photo.jpg']], [
            'attachments' => true,
            'custom_field_selection' => true,
            'included_fields' => ['first_name', 'cv'],
        ]);

        $this->assertSame(['cv.pdf'], array_map(fn ($attachment) => $attachment->getFilename(), $sent->getAttachments()));
    }

    private const CONTACT_FIELDS = [
        'first_name' => ['type' => 'text', 'display' => 'First name'],
        'address' => ['type' => 'text', 'display' => 'Address'],
        'email' => ['type' => 'text', 'display' => 'E-Mail'],
        'message' => ['type' => 'textarea', 'display' => 'Message'],
    ];

    private const CONTACT_DATA = [
        'first_name' => 'Anna',
        'address' => 'Main Street 1',
        'email' => 'anna@example.test',
        'message' => 'Hello there',
    ];

    private function assertFieldOrder(array $displays, string $body): void
    {
        $positions = array_map(fn (string $display) => strpos($body, $display), $displays);

        $this->assertNotContains(false, $positions, 'Missing field in: '.$body);
        $this->assertSame($positions, collect($positions)->sort()->values()->all(), 'Wrong field order in: '.$body);
    }

    private function sendEmail(array $fields, array $data, array $config = []): array
    {
        $sent = $this->send($fields, $data, $config);

        $this->assertStringNotContainsString('<', $sent->getTextBody());

        return array_map(fn (string $body): string => trim(preg_replace('/\s+/', ' ',
            str_replace('**', '', html_entity_decode(strip_tags(str_replace(['<br />', '<br>'], ' ', $body))))
        )), [$sent->getHtmlBody(), $sent->getTextBody()]);
    }

    private function send(array $fields, array $data, array $config = []): Email
    {
        $blueprint = Blueprint::makeFromFields($fields);
        Blueprint::partialMock()->shouldReceive('find')->with('forms.email_rendering_test')->andReturn($blueprint);

        $form = Form::make('email_rendering_test')->title('Email rendering test');
        $submission = (new Submission)->form($form)->data($data);
        $email = new FormEmail($submission, array_merge([
            'to' => 'validation@example.test',
            'html' => 'formbuilder::emails.submission',
            'markdown' => true,
        ], $config), Site::default());

        return Mail::mailer('array')->send($email)->getSymfonySentMessage()->getOriginalMessage();
    }
}
