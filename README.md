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
php artisan formbuilder:publish-example-form          # optional showcase form
```

| Tag / Command | Destination |
|-----|-------------|
| `formbuilder-blueprints` | `resources/blueprints/globals/form_builder.yaml` |
| `formbuilder-config` | `config/formbuilder.php` |
| `formbuilder-views` | `resources/views/vendor/formbuilder` |
| `formbuilder-translations` | `lang/vendor/formbuilder` |
| `formbuilder:publish-example-form` | Form + blueprint under configured Statamic paths |

After publishing the blueprints, create a global set with handle `form_builder`
and fill in the default button/status texts.

The example form command reads the project's configured Statamic sites and
writes a template form whose labels, hints, options, and other translatable
values include an entry for each site. Paths come from
`statamic.forms.forms` and `statamic.system.blueprints_path` (not hard-coded).
Re-run with `--force` to overwrite.

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
- Optional `formbuilder:publish-example-form` showcase covering every fieldtype.

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
    'default_email_markdown' => true,
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

Formbuilder email views (`user-submission`, `submission`) use Laravel's
`mail::message` component. With `default_email_markdown` enabled (the default),
the send job turns on Statamic's markdown email pipeline automatically whenever
an HTML template is configured — editors do not need to toggle Markdown in the
CP. Set `default_email_markdown` to `false` to restore Statamic's view-only
rendering, or disable Markdown per email in form settings.

The bundled email views can be selected with:

- `vendor/formbuilder/emails/user-submission` (shown in the CP template picker after publishing views)
- `vendor/formbuilder/emails/submission`

The namespaced equivalents `formbuilder::emails/user-submission` and
`formbuilder::emails/submission` also work in YAML, but the CP dropdown lists
the `vendor/formbuilder/…` paths when views are published.

To customize those templates, publish them with `--tag=formbuilder-views`. Edits
live under `resources/views/vendor/formbuilder` and override the addon views.
After upgrading the addon, diff your published copies against the package if you
rely on local overrides.

## Email configuration

Form emails are configured per form in the Statamic Control Panel under
**Configure Form → Email**. The addon adds translatable **E-Mail Text**
(`mail_text`) and an in-CP preview alongside Statamic's native fields.

### Variable substitution (Antlers)

Statamic parses **Antlers**, not Handlebars, in `to`, `from`, `reply_to`,
`subject`, and other string config values at send time. Use **double braces**
matching field handles from the form blueprint:

```yaml
to: '{{ email }}, admin@example.com'
subject:
  - handle: de
    value: 'Nachricht von {{ vorname }} {{ nachname }}'
mail_text:
  - handle: de
    value: '<p>Guten Tag {{ vorname }},</p><p>{{ mitteilung }}</p>'
```

Single braces like `{vorname}` are left as literal text in sent emails.

### Sender and recipient addresses

Use a valid RFC 2822 address. Either a bare email or a display name with email:

```yaml
from: 'Example Org <noreply@example.com>'
reply_to: admin@example.com
```

A display name alone (e.g. `from: 'Example Org'`) will fail when the email is sent.

### HTML templates

| Template | Use for |
|----------|---------|
| `vendor/formbuilder/emails/user-submission` | Confirmation to the submitter; body comes from translatable **E-Mail Text** |
| `vendor/formbuilder/emails/submission` | Notification to staff; lists all submitted fields |

Example minimal config:

```yaml
email:
  -
    to: '{{ email }}'
    from: 'Example Org <noreply@example.com>'
    subject:
      - handle: de
        value: 'We received your message'
    mail_text:
      - handle: de
        value: '<p>Thank you, {{ vorname }}.</p>'
    html: vendor/formbuilder/emails/user-submission
```

Markdown rendering for bundled templates is enabled automatically when
`default_email_markdown` is `true` (the default). Editors normally do not need
to change the Markdown toggle.

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
