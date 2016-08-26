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
    protected function getPackageProviders($app = null)
    {
        return ['Mariuzzo\LaravelJsLocalization\LaravelJsLocalizationServiceProvider'];
    }

    /**
     * Test the command.
     */
    public function testShouldRegisterProvider()
    {
        // TODO: Add some assertions. (however, this already test if this
        // package can be provided with the method: getPackageProviders).
    }
}
