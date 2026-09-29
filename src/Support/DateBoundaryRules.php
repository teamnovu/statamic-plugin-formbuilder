<?php

namespace Teamnovu\Formbuilder\Support;

use Closure;

final class DateBoundaryRules
{
    /**
     * @return list<string|Closure>
     */
    public static function forSingleDate(?string $earliest, ?string $latest): array
    {
        $rules = ['nullable', 'date_format:Y-m-d'];

        $earliestNormalized = DateBoundaryNormalizer::normalize($earliest);
        $latestNormalized = DateBoundaryNormalizer::normalize($latest);

        if ($earliestNormalized !== null) {
            $rules[] = 'after_or_equal:'.$earliestNormalized;
        }

        if ($latestNormalized !== null) {
            $rules[] = 'before_or_equal:'.$latestNormalized;
        }

        return $rules;
    }

    /**
     * @return list<string|Closure>
     */
    public static function forDateRange(?string $earliest, ?string $latest): array
    {
        $earliestNormalized = DateBoundaryNormalizer::normalize($earliest);
        $latestNormalized = DateBoundaryNormalizer::normalize($latest);

        $rules = ['nullable'];

        $rules[] = function (string $attribute, mixed $value, Closure $fail) use ($earliestNormalized, $latestNormalized): void {
            if ($value === null || $value === '') {
                return;
            }

            if (! is_string($value)) {
                $fail(__('validation.string'));

                return;
            }

            if (! preg_match('/^\d{4}-\d{2}-\d{2} - \d{4}-\d{2}-\d{2}$/', $value)) {
                $fail(__('validation.regex'));

                return;
            }

            [$start, $end] = explode(' - ', $value, 2);

            if ($start > $end) {
                $fail(__('validation.after_or_equal', ['date' => $end]));

                return;
            }

            if ($earliestNormalized !== null && ($start < $earliestNormalized || $end < $earliestNormalized)) {
                $fail(__('validation.after_or_equal', ['date' => $earliestNormalized]));

                return;
            }

            if ($latestNormalized !== null && ($start > $latestNormalized || $end > $latestNormalized)) {
                $fail(__('validation.before_or_equal', ['date' => $latestNormalized]));
            }
        };

        return $rules;
    }
}
