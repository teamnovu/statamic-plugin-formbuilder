<?php

namespace Teamnovu\Formbuilder\Support;

use Carbon\CarbonImmutable;

/**
 * Normalizes Statamic date boundary config to calendar dates (Y-m-d).
 *
 * Legacy form blueprints may store naive datetimes (Y-m-d H:i) as UTC wall time when
 * editors pick a date in a display timezone. Those values are converted using the
 * configured display timezone before validation and GraphQL consumers.
 */
final class DateBoundaryNormalizer
{
    private const DATE_ONLY = '!Y-m-d';

    private const DATETIME_MINUTE = '!Y-m-d H:i';

    private const DATETIME_SECOND = '!Y-m-d H:i:s';

    public static function normalize(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $string = trim((string) $value);

        if ($string === '') {
            return null;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $string) === 1) {
            return $string;
        }

        $utc = self::parseUtcWallDatetime($string);

        if ($utc === null) {
            return null;
        }

        return $utc->setTimezone(self::displayTimezone())->format('Y-m-d');
    }

    private static function displayTimezone(): string
    {
        $configured = config('statamic.system.display_timezone');

        if (is_string($configured) && $configured !== '') {
            return $configured;
        }

        return 'Europe/Zurich';
    }

    private static function parseUtcWallDatetime(string $string): ?CarbonImmutable
    {
        foreach ([self::DATETIME_MINUTE, self::DATETIME_SECOND] as $format) {
            try {
                $parsed = CarbonImmutable::createFromFormat($format, $string, 'UTC');
            } catch (\Throwable) {
                $parsed = false;
            }

            $outputFormat = ltrim($format, '!');

            if ($parsed !== false && $parsed->format($outputFormat) === $string) {
                return $parsed;
            }
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}/', $string) === 1) {
            try {
                return CarbonImmutable::parse(str_replace(' ', 'T', $string), 'UTC');
            } catch (\Throwable) {
                return null;
            }
        }

        try {
            $parsed = CarbonImmutable::createFromFormat(self::DATE_ONLY, $string, 'UTC');
        } catch (\Throwable) {
            return null;
        }

        if ($parsed !== false && $parsed->format('Y-m-d') === $string) {
            return $parsed->startOfDay();
        }

        return null;
    }
}
