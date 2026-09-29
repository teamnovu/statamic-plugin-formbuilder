<?php

namespace Teamnovu\Formbuilder\Tests;

use Illuminate\Support\Facades\Validator;
use Teamnovu\Formbuilder\Support\DateBoundaryRules;

class DateBoundaryRulesTest extends TestCase
{
    public function test_single_date_rejects_values_after_latest_boundary(): void
    {
        $rules = DateBoundaryRules::forSingleDate(null, '2026-12-31');

        $validator = Validator::make(
            ['visit_date' => '2027-01-01'],
            ['visit_date' => $rules]
        );

        $this->assertTrue($validator->fails());
    }

    public function test_single_date_accepts_value_on_latest_boundary(): void
    {
        $rules = DateBoundaryRules::forSingleDate(null, '2026-12-31');

        $validator = Validator::make(
            ['visit_date' => '2026-12-31'],
            ['visit_date' => $rules]
        );

        $this->assertFalse($validator->fails());
    }

    public function test_single_date_normalizes_legacy_latest_boundary(): void
    {
        config(['statamic.system.display_timezone' => 'Europe/Zurich']);

        $rules = DateBoundaryRules::forSingleDate(null, '2026-12-30 23:00');

        $validator = Validator::make(
            ['visit_date' => '2026-12-31'],
            ['visit_date' => $rules]
        );

        $this->assertFalse($validator->fails());

        $tooLate = Validator::make(
            ['visit_date' => '2027-01-01'],
            ['visit_date' => $rules]
        );

        $this->assertTrue($tooLate->fails());
    }

    public function test_date_range_rejects_endpoints_outside_boundaries(): void
    {
        $rules = DateBoundaryRules::forDateRange('2026-04-01', '2026-04-30');

        $validator = Validator::make(
            ['range' => '2026-03-31 - 2026-04-05'],
            ['range' => $rules]
        );

        $this->assertTrue($validator->fails());
    }

    public function test_date_range_rejects_start_after_end(): void
    {
        $rules = DateBoundaryRules::forDateRange(null, null);

        $validator = Validator::make(
            ['range' => '2026-04-10 - 2026-04-01'],
            ['range' => $rules]
        );

        $this->assertTrue($validator->fails());
    }

    public function test_date_range_accepts_valid_range(): void
    {
        $rules = DateBoundaryRules::forDateRange('2026-04-01', '2026-04-30');

        $validator = Validator::make(
            ['range' => '2026-04-01 - 2026-04-30'],
            ['range' => $rules]
        );

        $this->assertFalse($validator->fails());
    }
}
