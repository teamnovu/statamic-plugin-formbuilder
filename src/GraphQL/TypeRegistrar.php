<?php

namespace Teamnovu\Formbuilder\GraphQL;

use Statamic\Facades\GraphQL;
use Teamnovu\Formbuilder\GraphQL\Types\FieldType;

class TypeRegistrar extends \Statamic\GraphQL\TypeRegistrar
{
    public function register(): void
    {
        parent::register();

        // Must run after parent::register() — otherwise Statamic overwrites our FieldType.
        GraphQL::addType(FieldType::class);
    }
}
