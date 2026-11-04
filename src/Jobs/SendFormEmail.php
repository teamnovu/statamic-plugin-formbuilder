<?php

namespace Teamnovu\Formbuilder\Jobs;

use Illuminate\Support\Facades\Mail;
use Statamic\Forms\SendEmail;
use Teamnovu\Formbuilder\Mail\FormEmail;
use Teamnovu\Formbuilder\Support\LocalizedValueResolver;

/**
 * Extends Statamic's SendEmail to resolve translatable config values
 * (translatable_input/translatable_bard) to the submitted site's locale
 * and apply formbuilder email defaults before the mailable processes them.
 * Sends FormEmail, which applies the email's field selection.
 */
class SendFormEmail extends SendEmail
{
    public function handle()
    {
        // _site is set on the submission by the addon's FormSubmitted listener.
        $siteHandle = $this->submission->get('_site') ?? $this->site->handle();

        $this->config = app(LocalizedValueResolver::class)
            ->prepareForSending($this->config, $siteHandle);

        // Same as parent::handle(), which hard-codes Statamic's Email mailable.
        Mail::mailer($this->config['mailer'] ?? null)
            ->send(new FormEmail($this->submission, $this->config, $this->site));
    }
}
