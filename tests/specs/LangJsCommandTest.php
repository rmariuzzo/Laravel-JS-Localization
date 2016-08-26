<?php

namespace Mariuzzo\LaravelJsLocalization;

use Illuminate\Filesystem\Filesystem as File;
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
        $this->testPath = __DIR__.'/..';
        $this->rootPath = __DIR__.'/../..';
        $this->outputFilePath = "$this->testPath/output/lang.js";
        $this->langPath = "$this->testPath/fixtures/lang";
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
        $this->assertFileExists($this->outputFilePath);

        $contents = file_get_contents($this->outputFilePath);
        $this->assertNotEmpty($contents);
        $this->assertHasNotHandlebars('messages', $contents);
        $this->assertHasNotHandlebars('langjs', $contents);
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
        $this->assertContains('gm8ft2hrrlq1u6m54we9udi', $contents);
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
        $this->assertContains('en.messages', $contents);
        $this->assertNotContains('en.validation', $contents);
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
        $this->assertContains('en.forum.thread', $contents);
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
    protected function assertRunsWithSuccess($code, $message = null)
    {
        $this->assertEquals(0, $code, $message);
    }

    /**
     * @param string $handle
     * @param string $contents
     */
    protected function assertHasHandlebars($handle, $contents)
    {
        $this->assertEquals(1, preg_match('/\'\{(\s)'.preg_quote($handle).'(\s)\}\'/', $contents));
    }

    /**
     * @param string $handle
     * @param string $contents
     */
    protected function assertHasNotHandlebars($handle, $contents)
    {
        $this->assertEquals(0, preg_match('/\'\{(\s)'.preg_quote($handle).'(\s)\}\'/', $contents));
    }
}
