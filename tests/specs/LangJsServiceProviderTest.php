<?php

namespace Mariuzzo\LaravelJsLocalization;

use Illuminate\Support\Facades\File as FileFacade;
use Orchestra\Testbench\TestCase;

/**
 * The LangJsServiceProviderTest class.
 *
 * @author Rubens Mariuzzo <rubens@mariuzzo.com>
 */
class LangJsServiceProviderTest extends TestCase
{
    /**
     * The base path of tests.
     *
     * @var string
     */
    private $testPath;

    /**
     * The root path of the project.
     *
     * @var string
     */
    private $rootPath;

    /**
     * The file path of the expected output.
     *
     * @var string
     */
    private $outputFilePath;

    /**
     * The base path of language files.
     *
     * @var string
     */
    private $langPath;

    /**
     * LangJsCommandTest constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->testPath = __DIR__ . '/..';
        $this->rootPath = __DIR__ . '/../..';
        $this->outputFilePath = "$this->testPath/output/lang.js";
        $this->langPath = "$this->testPath/fixtures/lang";
    }

    protected function getPackageProviders($app = null)
    {
        return [
            'Mariuzzo\LaravelJsLocalization\LaravelJsLocalizationServiceProvider',
            'Mariuzzo\LaravelJsLocalization\NonameIncProvider',
        ];
    }

    /**
     * Test the command.
     */
    public function testShouldIncludePackageLangRegisteredInProvider()
    {

        $this->app->setBasePath(__DIR__ . '/../fixtures/');

        // this checks the LaravelJsLocalizationServiceProvider was loaded
        $this->artisan("lang:js {$this->outputFilePath}")
            ->assertExitCode(0);
        $this->assertFileExists($this->outputFilePath);

        $expected = '"en.nonameinc::messages":{"another_important_data":"this is from the package lang","important_data":"should have replaced packages value for this key","new_key":"this is a new key added ontop of the packages"}';
        $result = file_get_contents($this->outputFilePath);
        $this->_assertStringContainsString($expected, $result);

        $this->cleanupOutputDirectory();

    }

    /**
     * Cleanup output directory after tests.
     */
    protected function cleanupOutputDirectory()
    {
        $files = FileFacade::files("{$this->testPath}/output");
        foreach ($files as $file) {
            FileFacade::delete($file);
        }
    }

    public function _assertStringContainsString($needle, $haystack)
    {
        if (method_exists(get_parent_class($this), 'assertStringContainsString')) {
            return $this->assertStringContainsString($needle, $haystack);
        }

        return $this->assertContains($needle, $haystack);
    }
}
