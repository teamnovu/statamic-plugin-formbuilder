<?php

namespace Teamnovu\Formbuilder\Tests;

use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\Attributes\WithConfig;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Statamic\Facades\Blueprint;
use Statamic\Facades\Data;
use Statamic\Facades\Form;

#[WithConfig('statamic.graphql.resources.forms', true, defer: false)]
class GraphQLFieldConditionsTest extends TestCase
{
    public function test_it_exposes_conditions_with_link_resolution_enabled(): void
    {
        $this->assertFormFieldConditions('2026-04-01');
    }

    #[WithConfig('formbuilder.resolve_statamic_links_in_graphql', false, defer: false)]
    public function test_it_exposes_conditions_with_link_resolution_disabled(): void
    {
        $this->assertFormFieldConditions('2026-04-01 12:00');
    }

    private function assertFormFieldConditions(string $expectedEarliestDate): void
    {
        $any = ['children_one_adult' => '>= 1', 'children_two_adults' => '>= 1', 'children_three_adults' => '>= 1'];
        $if = ['group' => 'equals family'];
        $unless = ['destination' => 'equals statamic://entry::test'];
        Blueprint::setFallback('forms.conditions', fn () => Blueprint::makeFromFields([
            'child_name' => ['type' => 'input_text', 'if_any' => $any, 'unexposed' => true],
            'family' => ['type' => 'input_text', 'if' => $if],
            'destination' => ['type' => 'input_text', 'unless' => $unless],
            'date' => ['type' => 'input_date', 'earliest_date' => '2026-04-01 12:00'],
        ]));
        config(['statamic.forms.forms' => Storage::fake('forms')->path('')]);
        Form::make('conditions')->save();

        Data::shouldReceive('find')->with('statamic://entry::test')->never();

        $result = GraphQL::query('{ form(handle: "conditions") { fields { handle config if unless } } }');
        $this->assertArrayNotHasKey('errors', $result, json_encode($result['errors'] ?? []));
        $fields = array_column($result['data']['form']['fields'], null, 'handle');

        $this->assertSame(['if_any' => $any], $fields['child_name']['config']['conditions']);
        $this->assertArrayNotHasKey('unexposed', $fields['child_name']['config']);
        $this->assertSame($if, $fields['family']['if']);
        $this->assertSame(['if' => $if], $fields['family']['config']['conditions']);
        $this->assertSame($unless, $fields['destination']['unless']);
        $this->assertSame(['unless' => $unless], $fields['destination']['config']['conditions']);
        $this->assertArrayNotHasKey('conditions', $fields['date']['config']);
        $this->assertSame($expectedEarliestDate, $fields['date']['config']['earliest_date']);
    }
}
