<?php

return [
    /* Only expose this addon's input fieldtypes in Statamic's form blueprint picker. */
    'restrict_form_fieldtypes' => true,

    /* Add translatable subject/body fields and the email preview to form settings. */
    'extend_email_configuration' => true,

    /* Remember the submitting site so localized email values can be resolved. */
    'capture_submission_site' => true,

    /*
     | Replace Statamic's email job with the locale-aware implementation.
     | When true, overrides `statamic.forms.send_email_job` at boot —
     | you do not need to edit config/statamic/forms.php.
     */
    'use_localized_email_job' => true,

    /*
     | Enable Statamic's markdown email pipeline by default when an HTML template
     | is configured. Required for formbuilder email views that use mail::message.
     | Set to false to restore Statamic's default (view-only rendering).
     */
    'default_email_markdown' => true,

    /* Resolve statamic:// references embedded in form field config returned by GraphQL. */
    'resolve_statamic_links_in_graphql' => true,

    /* Enable floating labels. */
    'floating_label' => false,

    /* Show helper text fields in the Control Panel field config. */
    'show_help' => true,

    /* Show hint text fields in the Control Panel field config. */
    'show_hint' => true,

    /*
     | Enable one-click translation buttons on translatable_input fields.
     | Requires appswithlove/statamic-one-click-content-translation to be
     | installed and configured (TRANSLATION_DEEPL_AUTH_KEY or Google creds).
     */
    'enable_one_click_translation' => true,
];
