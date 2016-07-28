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
    private $outputFilePath;

    public function __construct()
    {
        $this->setOutputFilePath(__DIR__ . '/output/lang.js');
    }

    public function getOutputFilePath()
    {
        return $this->outputFilePath;
    }

    public function setOutputFilePath($filePath)
    {
        $this->outputFilePath = $filePath;
    }

    /**
     * Test the command.
     */
    public function testShouldCommandRun()
    {
        $generator = new LangJsGenerator(new File(), __DIR__ . '/fixtures/lang');

        $command = new LangJsCommand($generator);
        $command->setLaravel($this->app);

        $code = $this->runCommand($command, ['target' => $this->getOutputFilePath()]);

        $this->assertRunsWithSuccess($code);

        $this->assertFileExists($this->getOutputFilePath());

        $template = __DIR__ . '/../src/Mariuzzo/LaravelJsLocalization/Generators/Templates/langjs_with_messages.js';

        $this->assertFileExists($template);

        $this->assertFileNotEquals($template, $this->getOutputFilePath());
    }

    /**
     * @return void
     */
    public function testShouldTemplateHasHandlebars()
    {
        $template = __DIR__ . '/../src/Mariuzzo/LaravelJsLocalization/Generators/Templates/langjs_with_messages.js';

        $this->assertFileExists($template);

        $contents = file_get_contents($template);

        $this->assertNotEmpty($contents);

        $this->assertHasHandlebars('messages', $contents);

        $this->assertHasHandlebars('langjs', $contents);
    }

    /**
     * @return void
     */
    public function testShouldOutputHasNotHandlebars()
    {
        $this->assertFileExists($this->getOutputFilePath());

        $contents = file_get_contents($this->getOutputFilePath());

        $this->assertNotEmpty($contents);

        $this->assertHasNotHandlebars('messages', $contents);

        $this->assertHasNotHandlebars('langjs', $contents);
    }

    /**
     * @return void
     */
    public function testAllFilesShouldBeConverted()
    {
        $generator = new LangJsGenerator(new File(), __DIR__ . '/fixtures/lang');

        $command = new LangJsCommand($generator);
        $command->setLaravel($this->app);

        $code = $this->runCommand($command, ['target' => $this->getOutputFilePath()]);

        $this->assertRunsWithSuccess($code);

        $this->assertFileExists($this->getOutputFilePath());

        $contents = file_get_contents($this->getOutputFilePath());

        $this->assertContains('createCongregation', $contents);
    }

    /**
     * @return void
     */
    public function testFilesSelectedInConfigShouldBeConverted()
    {
        $this->app['config']->set('localization-js.messages', [
            'messages',
        ]);

        $generator = new LangJsGenerator(new File(), __DIR__ . '/fixtures/lang');

        $command = new LangJsCommand($generator);
        $command->setLaravel($this->app);

        $code = $this->runCommand($command, ['target' => $this->getOutputFilePath()]);

        $this->assertRunsWithSuccess($code);

        $this->assertFileExists($this->getOutputFilePath());

        $contents = file_get_contents($this->getOutputFilePath());

        $this->assertContains('en.messages', $contents);
        $this->assertNotContains('en.validation', $contents);
    }

    /**
     * Run the command.
     * @param \Illuminate\Console\Command $command
     * @param array $input
     * @return int
     */
    protected function runCommand($command, $input = [])
    {
        return $command->run(new ArrayInput($input), new NullOutput());
    }

    /**
     * Assert the code return is success.
     * @param int $code
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
}
