<?php

namespace Teamnovu\Formbuilder\Jobs;

use Statamic\Forms\SendEmail;
use Teamnovu\Formbuilder\Support\LocalizedValueResolver;

/**
 * Extends Statamic's SendEmail to resolve translatable config values
 * (translatable_input/translatable_bard) to the submitted site's locale
 * and apply formbuilder email defaults before the Email mailable processes them.
 */
class SendFormEmail extends SendEmail
{
    public function handle()
    {
        // _site is set on the submission by the addon's FormSubmitted listener.
        $siteHandle = $this->submission->get('_site') ?? $this->site->handle();

        $this->config = app(LocalizedValueResolver::class)
            ->prepareForSending($this->config, $siteHandle);

        parent::handle();
    }
}
