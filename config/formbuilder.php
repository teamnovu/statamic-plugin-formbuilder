<?php

return [
    /* Only expose this addon's input fieldtypes in Statamic's form blueprint picker. */
    'restrict_form_fieldtypes' => true,

    /* Add translatable subject/body fields and the email preview to form settings. */
    'extend_email_configuration' => true,

    /* Remember the submitting site so localized email values can be resolved. */
    'capture_submission_site' => true,

    /* Replace Statamic's email job with the locale-aware implementation. */
    'use_localized_email_job' => true,

    /* Resolve statamic:// references embedded in form field config returned by GraphQL. */
    'resolve_statamic_links_in_graphql' => true,
];
