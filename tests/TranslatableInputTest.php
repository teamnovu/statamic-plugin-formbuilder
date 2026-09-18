<?php

namespace Teamnovu\Formbuilder\Tests;

use Statamic\Facades\Site;
use Statamic\Facades\User;
use Teamnovu\Formbuilder\Fieldtypes\TranslatableInput;

class TranslatableInputTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $user = User::make()
            ->id('translatable-input-test-user')
            ->email('editor@example.com')
            ->data(['preferences' => ['locale' => 'de']])
            ->makeSuper();

        $this->actingAs($user);
    }

    public function test_preload_includes_default_site(): void
    {
        config(['formbuilder.enable_one_click_translation' => false]);

        $meta = app(TranslatableInput::class)->preload();

        $this->assertSame(Site::default()->handle(), $meta['defaultSite']);
    }

    public function test_preload_disables_one_click_translation_when_config_is_false(): void
    {
        config(['formbuilder.enable_one_click_translation' => false]);

        $meta = app(TranslatableInput::class)->preload();

        $this->assertFalse($meta['oneClickTranslation']);
        $this->assertNull($meta['oneClickTranslationLabels']);
    }

    public function test_preload_disables_one_click_translation_when_awl_addon_is_absent(): void
    {
        config(['formbuilder.enable_one_click_translation' => true]);

        $meta = app(TranslatableInput::class)->preload();

        $this->assertFalse($meta['oneClickTranslation']);
        $this->assertNull($meta['oneClickTranslationLabels']);
    }

    public function test_one_click_translation_strings_are_translatable(): void
    {
        app()->setLocale('en');

        $this->assertSame('Translate', __('formbuilder::form.one_click_translation.translate'));
        $this->assertSame(
            'Add a default-language value first.',
            __('formbuilder::form.one_click_translation.missing_source')
        );

        app()->setLocale('de');

        $this->assertSame('Übersetzen', __('formbuilder::form.one_click_translation.translate'));
    }
}
