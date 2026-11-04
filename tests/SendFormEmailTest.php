<?php

namespace Teamnovu\Formbuilder\Tests;

use Illuminate\Support\Facades\Mail;
use Statamic\Facades\Blueprint;
use Statamic\Facades\Form;
use Statamic\Facades\Site;
use Statamic\Forms\Submission;
use Teamnovu\Formbuilder\Jobs\SendFormEmail;
use Teamnovu\Formbuilder\Mail\FormEmail;

class SendFormEmailTest extends TestCase
{
    public function test_job_sends_the_field_selecting_mailable_with_the_selection_applied(): void
    {
        Mail::fake();

        $blueprint = Blueprint::makeFromFields([
            'first_name' => ['type' => 'text', 'display' => 'First name'],
            'message' => ['type' => 'textarea', 'display' => 'Message'],
        ]);
        Blueprint::partialMock()->shouldReceive('find')->with('forms.send_job_test')->andReturn($blueprint);

        $form = Form::make('send_job_test')->title('Send job test');
        $submission = (new Submission)->form($form)->data(['first_name' => 'Anna', 'message' => 'Hello there']);

        (new SendFormEmail($submission, Site::default(), [
            'to' => 'staff@example.test',
            'html' => 'formbuilder::emails.submission',
            'subject' => [['handle' => Site::default()->handle(), 'value' => 'New message']],
            'custom_field_selection' => true,
            'included_fields' => ['message'],
        ]))->handle();

        Mail::assertSent(FormEmail::class, function (FormEmail $email) {
            $html = $email->render();

            return $email->hasTo('staff@example.test')
                && $email->hasSubject('New message')
                && str_contains($html, 'Hello there')
                && ! str_contains($html, 'First name');
        });
    }
}
