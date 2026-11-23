<?php

namespace Teamnovu\Formbuilder\GraphQL\Types;

use Statamic\Facades\Data;
use Teamnovu\Formbuilder\Support\DateBoundaryNormalizer;

class FieldType extends \Statamic\GraphQL\Types\FieldType
{
    public function fields(): array
    {
        $fields = parent::fields();

        $resolveConfig = $fields['config']['resolve'];
        $fields['config']['resolve'] = function ($field) use ($resolveConfig) {
            $config = $resolveConfig($field);

            if (config('formbuilder.resolve_statamic_links_in_graphql')) {
                $config = $this->resolveStatamicLinks($this->normalizeDateBoundaries($config));
            }

            // Conditions can compare raw statamic:// values, so keep them outside link resolution.
            $conditions = $field->conditions();
            if ($conditions !== []) {
                $config['conditions'] = $conditions;
            }

            return $config;
        };

        return $fields;
    }

    /**
     * Recursively walks a config value and replaces any "statamic://type::id" string
     * with the resolved URL. Non-matching strings and non-string values pass through unchanged.
     */
    /**
     * @param  array<string, mixed>  $config
     * @return array<string, mixed>
     */
    private function normalizeDateBoundaries(array $config): array
    {
        foreach (['earliest_date', 'latest_date'] as $key) {
            if (! array_key_exists($key, $config)) {
                continue;
            }

            $normalized = DateBoundaryNormalizer::normalize($config[$key]);

            if ($normalized !== null) {
                $config[$key] = $normalized;
            }
        }

        return $config;
    }

    private function resolveStatamicLinks(mixed $value): mixed
    {
        if (is_string($value)) {
            return preg_replace_callback(
                '/statamic:\/\/([^"\'<>\s]+)/',
                fn ($m) => Data::find($m[1])?->url() ?? $m[0],
                $value
            );
        }

        if (is_array($value)) {
            return array_map(fn ($v) => $this->resolveStatamicLinks($v), $value);
        }

        return $value;
    }
}
