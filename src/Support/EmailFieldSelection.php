<?php

namespace Teamnovu\Formbuilder\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Statamic\Contracts\Forms\Form;

/**
 * The per-email field selection of Configure Form → Email: an email lists either
 * every field (toggle off) or only the selected handles, in the selected order.
 */
class EmailFieldSelection
{
    public const TOGGLE = 'custom_field_selection';

    public const FIELDS = 'included_fields';

    public static function isActive(array $config): bool
    {
        return (bool) ($config[self::TOGGLE] ?? false);
    }

    /**
     * Narrow renderable field data (arrays with a `handle` key) to the email's selection.
     * Selected handles that no longer exist are skipped and logged.
     */
    public static function filter(Collection $fields, array $config): Collection
    {
        if (! self::isActive($config)) {
            return $fields;
        }

        $byHandle = $fields->keyBy('handle');
        $selected = collect($config[self::FIELDS] ?? [])->filter(fn ($handle) => is_string($handle))->unique();

        if ($missing = $selected->reject(fn (string $handle) => $byHandle->has($handle))->values()->all()) {
            Log::warning('Formbuilder: email field selection contains fields that are not in the form blueprint.', [
                'missing' => $missing,
            ]);
        }

        return $selected
            ->filter(fn (string $handle) => $byHandle->has($handle))
            ->map(fn (string $handle) => $byHandle->get($handle))
            ->values();
    }

    /**
     * Follow blueprint changes in every email of the form: rename handles in place
     * (keeping their position) and drop deleted ones. Saves only when something changed.
     *
     * @param  array<string, string>  $renames  old handle => new handle
     * @param  array<int, string>  $deleted
     */
    public static function renameAndPrune(Form $form, array $renames, array $deleted): void
    {
        if (! $renames && ! $deleted) {
            return;
        }

        $changed = false;

        $emails = collect($form->email() ?? [])->map(function ($email) use ($renames, $deleted, &$changed) {
            if (! is_array($email) || ! is_array($email[self::FIELDS] ?? null)) {
                return $email;
            }

            $handles = collect($email[self::FIELDS])
                ->reject(fn ($handle) => in_array($handle, $deleted, true))
                ->map(fn ($handle) => $renames[$handle] ?? $handle)
                ->unique()
                ->values()
                ->all();

            if ($handles !== $email[self::FIELDS]) {
                $email[self::FIELDS] = $handles;
                $changed = true;
            }

            return $email;
        })->all();

        if ($changed) {
            $form->email($emails)->save();
        }
    }
}
