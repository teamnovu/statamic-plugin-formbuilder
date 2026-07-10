<?php

namespace Teamnovu\Formbuilder\Tests;

use Statamic\Testing\AddonTestCase;
use Teamnovu\Formbuilder\ServiceProvider;

abstract class TestCase extends AddonTestCase
{
    protected string $addonServiceProvider = ServiceProvider::class;
}
