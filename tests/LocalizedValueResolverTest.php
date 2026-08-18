<?php

namespace Teamnovu\Formbuilder\Tests;

use PHPUnit\Framework\Attributes\Test;
use Teamnovu\Formbuilder\Support\LocalizedValueResolver;

class LocalizedValueResolverTest extends TestCase
{
    #[Test]
    public function it_resolves_a_value_for_the_requested_site(): void
    {
        $value = [
            ['handle' => 'de', 'value' => 'Deutsch'],
            ['handle' => 'en', 'value' => 'English'],
        ];

        $this->assertSame('English', app(LocalizedValueResolver::class)->resolve($value, 'en'));
    }

    #[Test]
    public function it_falls_back_to_the_first_localized_value(): void
    {
        $value = [
            ['handle' => 'de', 'value' => 'Deutsch'],
            ['handle' => 'en', 'value' => 'English'],
        ];

        $this->assertSame('Deutsch', app(LocalizedValueResolver::class)->resolve($value, 'fr'));
    }

    #[Test]
    public function it_leaves_plain_configuration_values_unchanged(): void
    {
        $configuration = [
            'to' => 'hello@example.com',
            'markdown' => true,
            'attachments' => [],
        ];

        $this->assertSame(
            $configuration,
            app(LocalizedValueResolver::class)->resolveConfiguration($configuration, 'en')
        );
    }

    #[Test]
    public function it_enables_markdown_by_default_when_an_html_template_is_configured(): void
    {
        config(['formbuilder.default_email_markdown' => true]);

        $prepared = app(LocalizedValueResolver::class)->prepareForSending([
            'to' => 'hello@example.com',
            'html' => 'formbuilder::emails/user-submission',
        ], 'en');

        $this->assertTrue($prepared['markdown']);
    }

    #[Test]
    public function it_does_not_enable_markdown_when_explicitly_disabled(): void
    {
        config(['formbuilder.default_email_markdown' => true]);

        $prepared = app(LocalizedValueResolver::class)->prepareForSending([
            'to' => 'hello@example.com',
            'html' => 'formbuilder::emails/user-submission',
            'markdown' => false,
        ], 'en');

        $this->assertFalse($prepared['markdown']);
    }

    #[Test]
    public function it_does_not_enable_markdown_without_an_html_template(): void
    {
        config(['formbuilder.default_email_markdown' => true]);

        $prepared = app(LocalizedValueResolver::class)->prepareForSending([
            'to' => 'hello@example.com',
        ], 'en');

        $this->assertArrayNotHasKey('markdown', $prepared);
    }

    #[Test]
    public function it_respects_default_email_markdown_config_being_disabled(): void
    {
        config(['formbuilder.default_email_markdown' => false]);

        $prepared = app(LocalizedValueResolver::class)->prepareForSending([
            'to' => 'hello@example.com',
            'html' => 'formbuilder::emails/user-submission',
        ], 'en');

        $this->assertArrayNotHasKey('markdown', $prepared);
    }
}
