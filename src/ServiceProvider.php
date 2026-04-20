<?php

namespace Teamnovu\Formbuilder;

use Facades\Statamic\Fields\FieldtypeRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $vite = [ 
        'input' => [
            'resources/js/addon.js',
            'resources/css/addon.css',
        ],
        'publicDirectory' => 'resources/dist',
    ]; 
    public function bootAddon()
    {
        $this->loadTranslationsFrom(__DIR__.'/lang', 'formbuilder');
        $this->registerFormfields();
        $this->limitFormFieldtypeSelector();
    }

    private function registerFormfields(): void
    {
        collect([app()->getLocale(), config('app.fallback_locale')])
            ->filter(static fn ($locale): bool => is_string($locale) && $locale !== '')
            ->unique()
            ->each(function (string $locale): void {
                $path = __DIR__."/lang/{$locale}/form.php";

                if (! is_file($path)) {
                    return;
                }

                /** @var array<string, mixed> $translations */
                $translations = require $path;
                Lang::addLines(Arr::dot(['form' => $translations]), $locale);
            });
    }

    private function limitFormFieldtypeSelector(): void
    {
        $fieldtypeClasses = FieldtypeRepository::classes();

        $allowedHandles = $fieldtypeClasses
            ->filter(static fn (string $class): bool => str_starts_with($class, 'Teamnovu\\Formbuilder\\Fieldtypes\\'))
            ->map(static fn (string $class): string => $class::handle())
            ->filter(static fn (string $handle): bool => str_starts_with($handle, 'input_') || $handle === 'display_text')
            ->values()
            ->all();

        $fieldtypeClasses->keys()->each(static function (string $handle): void {
            FieldtypeRepository::makeUnselectableInForms($handle);
        });

        collect($allowedHandles)->each(static function (string $handle): void {
            FieldtypeRepository::makeSelectableInForms($handle);
        });

       
    }
}
