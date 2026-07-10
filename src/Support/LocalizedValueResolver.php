<?php

namespace Teamnovu\Formbuilder\Support;

class LocalizedValueResolver
{
    public function resolve(mixed $value, string $siteHandle): mixed
    {
        if (! is_array($value) || ! isset($value[0]) || ! is_array($value[0]) || ! array_key_exists('handle', $value[0])) {
            return $value;
        }

        $localizedValue = collect($value)->firstWhere('handle', $siteHandle);

        return $localizedValue['value'] ?? collect($value)->first()['value'] ?? '';
    }

    public function resolveConfiguration(array $configuration, string $siteHandle): array
    {
        return collect($configuration)
            ->map(fn (mixed $value): mixed => $this->resolve($value, $siteHandle))
            ->all();
    }
}
