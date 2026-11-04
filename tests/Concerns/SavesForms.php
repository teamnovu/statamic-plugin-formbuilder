<?php

namespace Teamnovu\Formbuilder\Tests\Concerns;

use Illuminate\Support\Facades\File;
use Statamic\Facades\Blueprint;
use Statamic\Facades\Form;
use Statamic\Facades\User;

// Saves real forms and form blueprints to disk and removes them after each test.
trait SavesForms
{
    private array $savedForms = [];

    protected function tearDown(): void
    {
        foreach ($this->savedForms as $handle) {
            File::delete([
                Form::make($handle)->path(),
                rtrim(config('statamic.system.blueprints_path'), '/').'/forms/'.$handle.'.yaml',
            ]);
        }

        parent::tearDown();
    }

    /**
     * @param  array<string, array>  $fields  handle => field config, saved in one section
     */
    protected function saveForm(string $handle, array $fields, array $emails = [])
    {
        $this->savedForms[] = $handle;

        Blueprint::make($handle)->setNamespace('forms')->setContents([
            'tabs' => ['main' => ['sections' => [[
                'fields' => collect($fields)
                    ->map(fn (array $field, string $fieldHandle) => ['handle' => $fieldHandle, 'field' => $field])
                    ->values()
                    ->all(),
            ]]]],
        ])->save();

        $form = Form::make($handle)->title(ucfirst($handle))->email($emails);
        $form->save();

        return Form::find($handle);
    }

    protected function actingAsSuperUser(): void
    {
        $this->actingAs(User::make()->id('saves-forms-test-user')->email('editor@example.com')->makeSuper());
    }
}
