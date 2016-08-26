<?php

namespace Mariuzzo\LaravelJsLocalization;

use Orchestra\Testbench\TestCase;

/**
 * The LangJsServiceProviderTest class.
 *
 * @author Rubens Mariuzzo <rubens@mariuzzo.com>
 */
class LangJsServiceProviderTest extends TestCase
{
    protected function getPackageProviders()
    {
        return ['Mariuzzo\LaravelJsLocalization\LaravelJsLocalizationServiceProvider'];
    }

    /**
     * Test the command.
     */
    public function testShouldRegisterProvider()
    {
    }
}
