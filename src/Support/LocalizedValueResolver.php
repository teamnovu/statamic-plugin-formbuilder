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

    public function prepareForSending(array $configuration, string $siteHandle): array
    {
        $configuration = collect($this->resolveConfiguration($configuration, $siteHandle))
            ->map(fn (mixed $value): mixed => is_string($value) ? $this->normalizeAntlersQuotes($value) : $value)
            ->all();

        if (! config('formbuilder.default_email_markdown', true)) {
            return $configuration;
        }

        if (empty($configuration['html'])) {
            return $configuration;
        }

        if (($configuration['markdown'] ?? null) === false) {
            return $configuration;
        }

        $configuration['markdown'] = true;

        return $configuration;
    }

    /**
     * Decode HTML entities inside Antlers tags so Bard-saved conditions like
     * {{ if foo == &quot;bar&quot; }} parse correctly alongside literal quotes.
     */
    public function normalizeAntlersQuotes(string $value): string
    {
        return preg_replace_callback(
            '/\{\{.*?\}\}/s',
            fn (array $matches): string => html_entity_decode($matches[0], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            $value,
        ) ?? $value;
    }
}
