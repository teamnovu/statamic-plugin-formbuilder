<?php

namespace Teamnovu\Formbuilder\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Statamic\Facades\Form;
use Statamic\Facades\YAML;
use Teamnovu\Formbuilder\Support\ExampleFormBlueprint;

class PublishExampleFormCommand extends Command
{
    protected $signature = 'formbuilder:publish-example-form {--force : Overwrite existing form and blueprint files}';

    protected $description = 'Publish a site-aware example form blueprint covering every formbuilder fieldtype';

    public function handle(): int
    {
        $blueprintPath = $this->blueprintPath();
        $formPath = $this->formPath();

        if (! $this->option('force') && (File::exists($blueprintPath) || File::exists($formPath))) {
            $this->error('Example form already exists. Re-run with --force to overwrite.');
            $this->line("  Blueprint: {$blueprintPath}");
            $this->line("  Form:      {$formPath}");

            return self::FAILURE;
        }

        $blueprint = new ExampleFormBlueprint;

        File::ensureDirectoryExists(dirname($blueprintPath));
        File::ensureDirectoryExists(dirname($formPath));

        File::put($blueprintPath, YAML::dump($blueprint->toArray()));
        File::put($formPath, YAML::dump([
            'title' => 'Template',
        ]));

        $sites = implode(', ', $blueprint->sites());

        $this->info('Published example form blueprint for sites: '.$sites);
        $this->line("  Blueprint: {$blueprintPath}");
        $this->line("  Form:      {$formPath}");

        return self::SUCCESS;
    }

    private function blueprintPath(): string
    {
        return rtrim(config('statamic.system.blueprints_path'), '/').'/forms/template.yaml';
    }

    private function formPath(): string
    {
        return Form::make('template')->path();
    }
}
