# novu Formbuilder

A Statamic 6 addon that turns the native form blueprint editor into a curated,
multilingual form builder.

## Setup

> [!IMPORTANT]
> Publishing `formbuilder-blueprints` is required. Without it, the `form_builder`
> global has no schema and default button/status texts cannot be edited in the CP.

```bash
composer require teamnovu/formbuilder
php artisan vendor:publish --tag=formbuilder-blueprints
php artisan vendor:publish --tag=formbuilder-config   # optional
php artisan vendor:publish --tag=formbuilder-views    # optional
```

| Tag | Destination |
|-----|-------------|
| `formbuilder-blueprints` | `resources/blueprints/globals/form_builder.yaml` |
| `formbuilder-config` | `config/formbuilder.php` |
| `formbuilder-views` | `resources/views/vendor/formbuilder` |
| `formbuilder-translations` | `lang/vendor/formbuilder` |

After publishing the blueprints, create a global set with handle `form_builder`
and fill in the default button/status texts.

For local development, point the consuming Statamic application's Composer path
repository at this checkout.

## Features

- Custom text, email, phone, number, date, range, select, checkbox, radio,
  switch, slider, upload, and display-text fieldtypes.
- Per-site labels, hints, options, and rich text through internal translatable
  fieldtypes.
- Locale-aware form submission views in the Control Panel.
- Translatable email subjects and bodies, including an in-CP HTML preview.
- Submission-site capture and locale resolution before Statamic sends email.
- Optional GraphQL resolution for `statamic://` links stored in field config.
- Ships a publishable `form_builder` global blueprint for default UI texts.

The site still owns its frontend form component and the `form_builder` global
set content.

## Configuration

Publish the config with `--tag=formbuilder-config`, then edit
`config/formbuilder.php`:

```php
return [
    'restrict_form_fieldtypes' => true,
    'extend_email_configuration' => true,
    'capture_submission_site' => true,
    'use_localized_email_job' => true,
    'resolve_statamic_links_in_graphql' => true,
    'floating_label' => false,
    'show_help' => true,
    'show_hint' => true,
];
```

`restrict_form_fieldtypes` deliberately hides every fieldtype except the addon's
public form inputs from the form blueprint picker. Set it to `false` when a site
needs native or third-party form fieldtypes as well.

Set `floating_label` to `true` to enable floating labels for text, email, textarea,
and select inputs. The toggle is hidden in the Control Panel; the value is still
written into each field's config for the frontend.

Set `show_help` or `show_hint` to `false` when the frontend does not support helper
or hint text. Those fields are then omitted from every input's Control Panel config.

## Localized submissions and email

Send the active Statamic site handle as `_site` in the form request, or as the
`X-Site` header. The addon stores it on the submission and resolves translated
email configuration for that site, falling back to the first configured value.

When `use_localized_email_job` is `true` (the default), the addon replaces
`statamic.forms.send_email_job` at boot with its locale-aware job. You do not
need to change `config/statamic/forms.php`. Set `use_localized_email_job` to
`false` only if you need a different custom email job.

The bundled email views can be selected with:

- `formbuilder::emails/user-submission`
- `formbuilder::emails/submission`

To customize those templates, publish them with `--tag=formbuilder-views`. Edits
live under `resources/views/vendor/formbuilder` and override the addon views.

## Development

Build Control Panel assets inside the addon:

```bash
composer install
pnpm install
pnpm run build
```

```bash
vendor/bin/phpunit
```
