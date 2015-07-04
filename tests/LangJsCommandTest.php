<?php

use Illuminate\Filesystem\Filesystem as File;
use Mariuzzo\LaravelJsLocalization\Commands\LangJsCommand;
use Mariuzzo\LaravelJsLocalization\Generators\LangJsGenerator;
use Orchestra\Testbench\TestCase;

/**
 * The LangJsCommandTest class.
 *
 * @author Rubens Mariuzzo <rubens@mariuzzo.com>
 */
class LangJsCommandTest extends  TestCase
{
    /**
     * Test the command.
     */
    public function testCommand()
    {
        $generator = new LangJsGenerator(new File, './tests/fixtures/lang');
        $command = new LangJsCommand($generator);
        $command->setLaravel($this->app);
        $this->runCommand($command, ['target' => './tests/output/lang.js']);
    }

    /**
     * Run the command.
     */
    protected function runCommand($command, $input = [])
    {
        return $command->run(new Symfony\Component\Console\Input\ArrayInput($input), new Symfony\Component\Console\Output\NullOutput);
    }
}
