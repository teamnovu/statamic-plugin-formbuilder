<?php

namespace Teamnovu\Formbuilder\Mail;

use Statamic\Contracts\Forms\Submission;
use Statamic\Forms\Email;
use Statamic\Sites\Site;
use Teamnovu\Formbuilder\Support\EmailFieldSelection;

/**
 * Statamic's form email, limited to the email's field selection. The `fields` list
 * and the attachments follow the selection; placeholders in to/subject/E-Mail Text
 * still see every submitted value.
 */
class FormEmail extends Email
{
    protected array $fieldSelection;

    public function __construct(Submission $submission, array $config, Site $site)
    {
        // Taken out before Email::parseConfig() runs every value through Antlers,
        // which fails on the array of selected handles.
        $this->fieldSelection = [
            EmailFieldSelection::TOGGLE => $config[EmailFieldSelection::TOGGLE] ?? false,
            EmailFieldSelection::FIELDS => $config[EmailFieldSelection::FIELDS] ?? [],
        ];

        unset($config[EmailFieldSelection::TOGGLE], $config[EmailFieldSelection::FIELDS]);

        parent::__construct($submission, $config, $site);
    }

    // Used by both addData() (the field list) and addAttachments().
    protected function getRenderableFieldData($values)
    {
        return EmailFieldSelection::filter(parent::getRenderableFieldData($values), $this->fieldSelection);
    }
}
