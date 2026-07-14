<?php

namespace Teamnovu\Formbuilder;

use Facades\Statamic\Fields\FieldtypeRepository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Statamic\Events\FormSubmitted;
use Statamic\Facades\Site;
use Statamic\GraphQL\TypeRegistrar as StatamicTypeRegistrar;
use Statamic\Http\Controllers\CP\Forms\FormsController as StatamicFormsController;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Statamic;
use Teamnovu\Formbuilder\GraphQL\TypeRegistrar;
use Teamnovu\Formbuilder\Http\Controllers\CP\FormEmailPreviewController;
use Teamnovu\Formbuilder\Http\Controllers\CP\FormsController;
use Teamnovu\Formbuilder\Jobs\SendFormEmail;

class ServiceProvider extends AddonServiceProvider
{
    protected $vite = [
        'input' => [
            'resources/js/addon.js',
            'resources/css/addon.css',
        ],
        'publicDirectory' => 'resources/dist',
    ];

    protected $viewNamespace = 'formbuilder';

    public function register(): void
    {
        parent::register();

        $this->mergeConfigFrom(__DIR__.'/../config/formbuilder.php', 'formbuilder');

        if (config('formbuilder.resolve_statamic_links_in_graphql')) {
            $this->app->bind(StatamicTypeRegistrar::class, TypeRegistrar::class);
        }

        if (config('formbuilder.extend_email_configuration')) {
            $this->app->bind(StatamicFormsController::class, FormsController::class);
        }
    }

    public function bootAddon(): void
    {
        $this->publishViews();
        $this->publishBlueprints();

        if (config('formbuilder.use_localized_email_job')) {
            config(['statamic.forms.send_email_job' => SendFormEmail::class]);
        }

        if (config('formbuilder.restrict_form_fieldtypes')) {
            $this->limitFormFieldtypeSelector();
        }

        if (config('formbuilder.capture_submission_site')) {
            $this->captureSubmissionSite();
        }

        if (config('formbuilder.extend_email_configuration')) {
            $this->registerEmailPreviewRoute();
        }
    }

    private function publishViews(): void
    {
        $this->publishes([
            $this->getAddon()->directory().'resources/views' => resource_path('views/vendor/formbuilder'),
        ], 'formbuilder-views');
    }

    private function publishBlueprints(): void
    {
        $this->publishes([
            $this->getAddon()->directory().'resources/blueprints/globals/form_builder.yaml'
                => resource_path('blueprints/globals/form_builder.yaml'),
        ], 'formbuilder-blueprints');
    }

    private function limitFormFieldtypeSelector(): void
    {
        $classes = FieldtypeRepository::classes();

        $allowedHandles = $classes
            ->filter(static fn (string $class): bool => str_starts_with($class, __NAMESPACE__.'\\Fieldtypes\\'))
            ->map(static fn (string $class): string => $class::handle())
            ->filter(static fn (string $handle): bool => str_starts_with($handle, 'input_') || $handle === 'display_text')
            ->values();

        $classes->keys()->each(
            static fn (string $handle) => FieldtypeRepository::makeUnselectableInForms($handle)
        );

        $allowedHandles->each(
            static fn (string $handle) => FieldtypeRepository::makeSelectableInForms($handle)
        );
    }

    private function captureSubmissionSite(): void
    {
        Event::listen(FormSubmitted::class, static function (FormSubmitted $event): void {
            $handle = request()->input('_site') ?? request()->header('X-Site');

            if ($handle && Site::get($handle)) {
                $event->submission->set('_site', $handle);
            }
        });
    }

    private function registerEmailPreviewRoute(): void
    {
        Statamic::pushCpRoutes(static function (): void {
            Route::get('forms/{form}/email-preview', [FormEmailPreviewController::class, 'show'])
                ->name('forms.email-preview');
        });
    }
}
