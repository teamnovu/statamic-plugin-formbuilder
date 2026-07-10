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
}
