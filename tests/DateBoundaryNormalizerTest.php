<?php

namespace Teamnovu\Formbuilder\Tests;

use Teamnovu\Formbuilder\Support\DateBoundaryNormalizer;

class DateBoundaryNormalizerTest extends TestCase
{
    public function test_it_passes_through_date_only_values(): void
    {
        $this->assertSame('2026-04-01', DateBoundaryNormalizer::normalize('2026-04-01'));
    }

    public function test_it_converts_legacy_utc_wall_datetime_to_display_timezone_calendar_date(): void
    {
        config(['statamic.system.display_timezone' => 'Europe/Zurich']);

        $this->assertSame(
            '2026-12-31',
            DateBoundaryNormalizer::normalize('2026-12-30 23:00')
        );
    }

    public function test_it_returns_null_for_empty_values(): void
    {
        $this->assertNull(DateBoundaryNormalizer::normalize(null));
        $this->assertNull(DateBoundaryNormalizer::normalize(''));
        $this->assertNull(DateBoundaryNormalizer::normalize('   '));
    }

    public function test_it_returns_null_for_unrecognized_values(): void
    {
        $this->assertNull(DateBoundaryNormalizer::normalize('not-a-date'));
    }
}
