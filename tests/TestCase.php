<?php

namespace Mariuzzo\LaravelJsLocalization\Tests;

use Illuminate\Support\Facades\File;
use Mariuzzo\LaravelJsLocalization\LaravelJsLocalizationServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra {

    protected function getPackageProviders($app) {
        return [
            LaravelJsLocalizationServiceProvider::class,
        ];
    }

    /**
     * @param string $handle
     * @param string $contents
     */
    protected function assertHasHandlebars($handle, $contents) {
        $this->assertEquals(1, preg_match('/\'\{(\s)' . preg_quote($handle) . '(\s)\}\'/', $contents));
    }

    /**
     * @param string $handle
     * @param string $contents
     */
    protected function assertHasNotHandlebars($handle, $contents) {
        $this->assertEquals(0, preg_match('/\'\{(\s)' . preg_quote($handle) . '(\s)\}\'/', $contents));
    }

    /**
     * Cleanup output directory after tests.
     */
    protected function cleanupOutputDirectory($testpath) {
        $files = File::files("{$testpath}/output");
        foreach ($files as $file) {
            File::delete($file);
        }
    }
}
