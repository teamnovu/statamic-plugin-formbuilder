<?php

namespace Teamnovu\Formbuilder\Tests;

use Facades\Statamic\Fields\FieldtypeRepository;
use Illuminate\Support\Facades\Route;
use ReflectionMethod;
use Statamic\Facades\Form;
use Statamic\GraphQL\TypeRegistrar as StatamicTypeRegistrar;
use Statamic\Http\Controllers\CP\Forms\FormsController as StatamicFormsController;
use Teamnovu\Formbuilder\Fieldtypes\InputText;
use Teamnovu\Formbuilder\GraphQL\TypeRegistrar;
use Teamnovu\Formbuilder\Http\Controllers\CP\FormsController;
use Teamnovu\Formbuilder\Jobs\SendFormEmail;

class AddonRegistrationTest extends TestCase
{
    public function test_it_registers_the_formbuilder_integrations(): void
    {
        $this->assertInstanceOf(FormsController::class, app(StatamicFormsController::class));
        $this->assertInstanceOf(TypeRegistrar::class, app(StatamicTypeRegistrar::class));
        $this->assertSame(SendFormEmail::class, config('statamic.forms.send_email_job'));
        $this->assertSame(InputText::class, FieldtypeRepository::classes()->get('input_text'));
        $this->assertTrue(Route::has('statamic.cp.forms.email-preview'));
        $this->assertTrue(view()->exists('formbuilder::emails.submission'));
    }

    public function test_it_registers_publishable_config_and_views(): void
    {
        $config = \Illuminate\Support\ServiceProvider::pathsToPublish(null, 'formbuilder-config');
        $views = \Illuminate\Support\ServiceProvider::pathsToPublish(null, 'formbuilder-views');

        $this->assertArrayHasKey(
            realpath(__DIR__.'/../config/formbuilder.php'),
            collect($config)->mapWithKeys(fn ($to, $from) => [realpath($from) => $to])->all()
        );
        $this->assertSame(config_path('formbuilder.php'), $config[array_key_first($config)]);

        $this->assertArrayHasKey(
            realpath(__DIR__.'/../resources/views'),
            collect($views)->mapWithKeys(fn ($to, $from) => [realpath($from) => $to])->all()
        );
        $this->assertSame(resource_path('views/vendor/formbuilder'), $views[array_key_first($views)]);
    }

    public function test_it_loads_namespaced_translations(): void
    {
        app()->setLocale('en');

        $this->assertSame('Form Input Text', __('formbuilder::form.title.text'));
    }

    public function test_it_extends_statamics_email_blueprint_without_replacing_it(): void
    {
        $controller = app(StatamicFormsController::class);
        $method = new ReflectionMethod($controller, 'editFormBlueprint');
        $blueprint = $method->invoke($controller, Form::make('contact'));
        $emailFields = collect($blueprint->field('email')->config()['fields'])->keyBy('handle');

        $this->assertSame('translatable_input', $emailFields['subject']['field']['type']);
        $this->assertSame('translatable_bard', $emailFields['mail_text']['field']['type']);
        $this->assertSame('html', $emailFields['mail_preview']['field']['type']);
    }
}
