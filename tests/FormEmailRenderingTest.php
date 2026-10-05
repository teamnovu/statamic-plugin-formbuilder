<?php

namespace Teamnovu\Formbuilder\Tests;

use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\DataProvider;
use Statamic\Facades\Blueprint;
use Statamic\Facades\Form;
use Statamic\Facades\Site;
use Statamic\Forms\Email;
use Statamic\Forms\Submission;

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

    private function sendEmail(array $fields, array $data, array $config = []): array
    {
        $blueprint = Blueprint::makeFromFields($fields);
        Blueprint::partialMock()->shouldReceive('find')->with('forms.email_rendering_test')->andReturn($blueprint);

        $form = Form::make('email_rendering_test')->title('Email rendering test');
        $submission = (new Submission)->form($form)->data($data);
        $email = new Email($submission, array_merge([
            'to' => 'validation@example.test',
            'html' => 'formbuilder::emails.submission',
            'markdown' => true,
        ], $config), Site::default());

        $sent = Mail::mailer('array')->send($email)->getSymfonySentMessage()->getOriginalMessage();

        $this->assertStringNotContainsString('<', $sent->getTextBody());

        return array_map(fn (string $body): string => trim(preg_replace('/\s+/', ' ',
            str_replace('**', '', html_entity_decode(strip_tags(str_replace(['<br />', '<br>'], ' ', $body))))
        )), [$sent->getHtmlBody(), $sent->getTextBody()]);
    }
}
