# Formbuilder

Formbuilder is a Statamic 6 addon that provides a focused set of custom form input fieldtypes for the Control Panel.

It is designed for teams that want consistent, translatable form input configuration across projects, while keeping the form fieldtype selector limited to your custom input set.

## Features

- Adds custom `input_*` fieldtypes for form building in Statamic.
- Includes a reusable `translatable_input` config fieldtype for multilingual labels, placeholders, hints, and help text.
- Restricts form fieldtype selection to Formbuilder input fieldtypes for a cleaner authoring experience.
- Ships with translations for `en`, `de`, and `fr`.
- Provides custom CP Vue components for author-friendly field configuration.

## Included Fieldtypes

- `input_text`
- `input_textarea`
- `input_email`
- `input_telephone`
- `input_number`
- `input_date`
- `input_daterange`
- `input_slider`
- `input_switch`
- `input_select`
- `input_checkboxes`
- `input_radio_buttons`
- `input_file_upload`
- `translatable_input` (used inside field configuration)

## Requirements

- PHP with Composer
- Statamic `^6.0`
- Node.js + pnpm (for Control Panel asset development)

## Installation

Install the addon via Composer:

```bash
composer require teamnovu/formbuilder
```

The service provider is auto-discovered via Laravel package discovery.

## Usage

1. Open your form blueprint in the Statamic Control Panel.
2. Add fields using the provided `Input ...` fieldtypes.
3. Configure translatable labels and helper texts where available.
4. Save and publish your blueprint.

Formbuilder automatically marks non-Formbuilder fieldtypes as unselectable in forms and re-enables only the supported `input_*` types.

## Local Development

Install JS dependencies:

```bash
pnpm install
```

Run Vite in development mode:

```bash
pnpm run dev
```

Build production assets:

```bash
pnpm run build
```

## Package Details

- Package: `teamnovu/formbuilder`
- Namespace: `Teamnovu\Formbuilder`
- Main provider: `Teamnovu\Formbuilder\ServiceProvider`

## License

Proprietary unless stated otherwise by the package owner.
