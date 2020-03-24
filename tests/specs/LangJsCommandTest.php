<?php

namespace Mariuzzo\LaravelJsLocalization;

use Config;
use Exception;
use Illuminate\Filesystem\Filesystem as File;
use Illuminate\Support\Facades\File as FileFacade;
use Mariuzzo\LaravelJsLocalization\Commands\LangJsCommand;
use Mariuzzo\LaravelJsLocalization\Generators\LangJsGenerator;
use Orchestra\Testbench\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * The LangJsCommandTest class.
 *
 * @author Rubens Mariuzzo <rubens@mariuzzo.com>
 */
class LangJsCommandTest extends TestCase
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

        $this->testPath       = __DIR__ . '/..';
        $this->rootPath       = __DIR__ . '/../..';
        $this->outputFilePath = "$this->testPath/output/lang.js";
        $this->langPath       = "$this->testPath/fixtures/lang";
    }

    public function _assertStringContainsString($needle, $haystack)
    {
        if (method_exists(get_parent_class($this), 'assertStringContainsString')) {
            return $this->assertStringContainsString($needle, $haystack);
        }

        return $this->assertContains($needle, $haystack);
    }

    public function _assertStringNotContainsString($needle, $haystack)
    {
        if (method_exists(get_parent_class($this), 'assertStringNotContainsString')) {
            return $this->assertStringNotContainsString($needle, $haystack);
        }

        return $this->assertNotContains($needle, $haystack);
    }

    /**
     * Test the command.
     */
    public function testShouldCommandRun()
    {
        $generator = new LangJsGenerator(new File(), $this->langPath);

        $command = new LangJsCommand($generator);
        $command->setLaravel($this->app);

        $code = $this->runCommand($command, ['target' => $this->outputFilePath]);
        $this->assertRunsWithSuccess($code);
        $this->assertFileExists($this->outputFilePath);

        $template = "$this->rootPath/src/Mariuzzo/LaravelJsLocalization/Generators/Templates/langjs_with_messages.js";
        $this->assertFileExists($template);
        $this->assertFileNotEquals($template, $this->outputFilePath);

        $this->cleanupOutputDirectory();
    }

    /**
     */
    public function testShouldTemplateHasHandlebars()
    {
        $template = "$this->rootPath/src/Mariuzzo/LaravelJsLocalization/Generators/Templates/langjs_with_messages.js";
        $this->assertFileExists($template);

        $contents = file_get_contents($template);
        $this->assertNotEmpty($contents);
        $this->assertHasHandlebars('messages', $contents);
        $this->assertHasHandlebars('langjs', $contents);
    }

    /**
     */
    public function testShouldOutputHasNotHandlebars()
    {
        $generator = new LangJsGenerator(new File(), $this->langPath);

        $command = new LangJsCommand($generator);
        $command->setLaravel($this->app);

        $code = $this->runCommand($command, ['target' => $this->outputFilePath]);
        $this->assertRunsWithSuccess($code);
        $this->assertFileExists($this->outputFilePath);

        $contents = file_get_contents($this->outputFilePath);
        $this->assertNotEmpty($contents);
        $this->assertHasNotHandlebars('messages', $contents);
        $this->assertHasNotHandlebars('langjs', $contents);

        $this->cleanupOutputDirectory();
    }

    /**
     */
    public function testAllFilesShouldBeConverted()
    {
        $generator = new LangJsGenerator(new File(), $this->langPath);

        $command = new LangJsCommand($generator);
        $command->setLaravel($this->app);

        $code = $this->runCommand($command, ['target' => $this->outputFilePath]);
        $this->assertRunsWithSuccess($code);
        $this->assertFileExists($this->outputFilePath);

        $contents = file_get_contents($this->outputFilePath);

        $this->_assertStringContainsString('gm8ft2hrrlq1u6m54we9udi', $contents);

        $this->_assertStringNotContainsString('vendor.nonameinc.en.messages', $contents);
        $this->_assertStringNotContainsString('vendor.nonameinc.es.messages', $contents);
        $this->_assertStringNotContainsString('vendor.nonameinc.ht.messages', $contents);

        $this->_assertStringContainsString('en.nonameinc::messages', $contents);
        $this->_assertStringContainsString('es.nonameinc::messages', $contents);
        $this->_assertStringContainsString('ht.nonameinc::messages', $contents);

        $this->_assertStringContainsString('en.forum.thread', $contents);

        $this->cleanupOutputDirectory();
    }

    /**
     */
    public function testFilesSelectedInConfigShouldBeConverted()
    {
        $generator = new LangJsGenerator(new File(), $this->langPath, ['messages']);

        $command = new LangJsCommand($generator);
        $command->setLaravel($this->app);

        $code = $this->runCommand($command, ['target' => $this->outputFilePath]);
        $this->assertRunsWithSuccess($code);
        $this->assertFileExists($this->outputFilePath);

        $contents = file_get_contents($this->outputFilePath);
        $this->_assertStringContainsString('en.messages', $contents);
        $this->_assertStringNotContainsString('en.validation', $contents);

        $this->cleanupOutputDirectory();
    }

    /**
     */
    public function testShouldIncludeNestedDirectoryFile()
    {
        $generator = new LangJsGenerator(new File(), $this->langPath, ['forum/thread']);

        $command = new LangJsCommand($generator);
        $command->setLaravel($this->app);

        $code = $this->runCommand($command, ['target' => $this->outputFilePath]);
        $this->assertRunsWithSuccess($code);
        $this->assertFileExists($this->outputFilePath);

        $contents = file_get_contents($this->outputFilePath);
        $this->_assertStringContainsString('en.forum.thread', $contents);

        $this->cleanupOutputDirectory();
    }

    /**
     */
    public function testShouldUseDefaultOutputPathFromConfig()
    {
        $customOutputFilePath = "{$this->testPath}/output/lang-with-custom-path.js";
        Config::set('localization-js.path', $customOutputFilePath);

        $generator = new LangJsGenerator(new File(), $this->langPath);

        $command = new LangJsCommand($generator);
        $command->setLaravel($this->app);

        $code = $this->runCommand($command);
        $this->assertRunsWithSuccess($code);
        $this->assertFileExists($customOutputFilePath);

        $template = "$this->rootPath/src/Mariuzzo/LaravelJsLocalization/Generators/Templates/langjs_with_messages.js";
        $this->assertFileExists($template);
        $this->assertFileNotEquals($template, $customOutputFilePath);

        $this->cleanupOutputDirectory();
    }

    /**
     */
    public function testShouldIgnoreDefaultOutputPathFromConfigIfTargetArgumentExist()
    {
        $customOutputFilePath = "{$this->testPath}/output/lang-with-custom-path.js";
        Config::set('localization-js.path', $customOutputFilePath);

        $generator = new LangJsGenerator(new File(), $this->langPath);

        $command = new LangJsCommand($generator);
        $command->setLaravel($this->app);

        $code = $this->runCommand($command, ['target' => $this->outputFilePath]);
        $this->assertRunsWithSuccess($code);
        $this->assertFileExists($this->outputFilePath);
        $this->assertFileNotExists($customOutputFilePath);

        $template = "$this->rootPath/src/Mariuzzo/LaravelJsLocalization/Generators/Templates/langjs_with_messages.js";
        $this->assertFileExists($template);
        $this->assertFileNotEquals($template, $this->outputFilePath);

        $this->cleanupOutputDirectory();
    }

    /*
     * test template have handlebar { messages }
     * */
    public function testShouldTemplateMessagesHasHandlebars()
    {
        $template = "$this->rootPath/src/Mariuzzo/LaravelJsLocalization/Generators/Templates/messages.js";
        $this->assertFileExists($template);

        $contents = file_get_contents($template);
        $this->assertNotEmpty($contents);
        $this->assertHasHandlebars('messages', $contents);
    }

    /*
     * test command with option --no-lib
     * */
    public function testShouldOnlyMessageExported()
    {
        $generator = new LangJsGenerator(new File(), $this->langPath);
        $command   = new LangJsCommand($generator);
        $command->setLaravel($this->app);

        $code = $this->runCommand($command, ['target' => $this->outputFilePath,'--no-lib' => true]);
        $this->assertRunsWithSuccess($code);
        $this->assertFileExists($this->outputFilePath);

        $contents = file_get_contents($this->outputFilePath);
        $this->assertNotEmpty($contents);
        $this->assertHasNotHandlebars('messages', $contents);
        $this->cleanupOutputDirectory();
    }

    /*
     * test command with option --json
     * */
    public function testShouldOnlyMessageJSONExported()
    {
        $generator = new LangJsGenerator(new File(), $this->langPath);
        $command   = new LangJsCommand($generator);
        $command->setLaravel($this->app);

        $code = $this->runCommand($command, ['target' => $this->outputFilePath,'--json' => true]);
        $this->assertRunsWithSuccess($code);
        $this->assertFileExists($this->outputFilePath);

        $contents = file_get_contents($this->outputFilePath);
        $this->assertNotEmpty($contents);
        $this->assertHasNotHandlebars('messages', $contents);
        $this->cleanupOutputDirectory();
    }

    /**
     */
    public function testChangeDefaultLangSourceFolder()
    {
        $generator = new LangJsGenerator(new File(), $this->langPath);

        $command = new LangJsCommand($generator);
        $command->setLaravel($this->app);

        $code = $this->runCommand(
            $command,
            [
                'target' => $this->outputFilePath,
                '-s'     => "$this->testPath/fixtures/theme/lang",
            ]
        );
        $this->assertRunsWithSuccess($code);
        $this->assertFileExists($this->outputFilePath);

        $template = "$this->rootPath/src/Mariuzzo/LaravelJsLocalization/Generators/Templates/langjs_with_messages.js";
        $this->assertFileExists($template);
        $this->assertFileNotEquals($template, $this->outputFilePath);

        $contents = file_get_contents($this->outputFilePath);
        $this->_assertStringContainsString('en.page', $contents);

        $this->cleanupOutputDirectory();
    }

    public function testChangeDefaultLangSourceFolderForOneThatDosentExist()
    {
        $generator = new LangJsGenerator(new File(), $this->langPath);

        $command = new LangJsCommand($generator);
        $command->setLaravel($this->app);

        try {
            $code = $this->runCommand(
                $command,
                [
                    'target' => $this->outputFilePath,
                    '-s'     => $this->langPath . '/non-exist',
                ]
            );
        } catch (Exception $exception) {
            return $this->assertTrue(true);
        }

        return $this->fail('Should have thrown Exception');
    }

    /**
     * Test that messages are sorted alphabetically by default.
     */
    public function testDoesSortMessages()
    {
        $generator = new LangJsGenerator(new File(), $this->langPath, ['pagination']);

        $command = new LangJsCommand($generator);
        $command->setLaravel($this->app);

        $code = $this->runCommand($command, ['target' => $this->outputFilePath]);
        $this->assertRunsWithSuccess($code);
        $this->assertFileExists($this->outputFilePath);

        $contents = file_get_contents($this->outputFilePath);
        $this->_assertStringContainsString('en.pagination', $contents);
        $this->_assertStringContainsString('{"next":"Next &raquo;","previous":"&laquo; Previous"}', $contents);

        $this->cleanupOutputDirectory();
    }

    /**
     * Tests that the --no-sort option does not sort messages.
     */
    public function testDoesNotSortMessages()
    {
        $generator = new LangJsGenerator(new File(), $this->langPath, ['pagination']);

        $command = new LangJsCommand($generator);
        $command->setLaravel($this->app);

        $code = $this->runCommand($command, ['target' => $this->outputFilePath, '--no-sort' => true]);
        $this->assertRunsWithSuccess($code);
        $this->assertFileExists($this->outputFilePath);

        $contents = file_get_contents($this->outputFilePath);
        $this->_assertStringContainsString('en.pagination', $contents);
        $this->_assertStringContainsString('{"previous":"&laquo; Previous","next":"Next &raquo;"}', $contents);

        $this->cleanupOutputDirectory();
    }

    /**
     * Run the command.
     *
     * @param \Illuminate\Console\Command $command
     * @param array                       $input
     *
     * @return int
     */
    protected function runCommand($command, $input = [])
    {
        return $command->run(new ArrayInput($input), new NullOutput());
    }

    /**
     * Assert the code return is success.
     *
     * @param int  $code
     * @param null $message
     */
    protected function assertRunsWithSuccess($code, $message = '')
    {
        $this->assertEquals(0, $code, $message);
    }

    /**
     * @param string $handle
     * @param string $contents
     */
    protected function assertHasHandlebars($handle, $contents)
    {
        $this->assertEquals(1, preg_match('/\'\{(\s)' . preg_quote($handle) . '(\s)\}\'/', $contents));
    }

    /**
     * @param string $handle
     * @param string $contents
     */
    protected function assertHasNotHandlebars($handle, $contents)
    {
        $this->assertEquals(0, preg_match('/\'\{(\s)' . preg_quote($handle) . '(\s)\}\'/', $contents));
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
}
