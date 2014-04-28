<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Filesystem\Filesystem as File;
use Mariuzzo\LaravelJsLocalization\Commands\LangJsCommand;
use Mariuzzo\LaravelJsLocalization\Generators\LangJsGenerator;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as M;

class LangJsCommandTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $app = M::mock('Application');

        $app = $app->shouldReceive('make')
            ->with('path.public')
            ->andReturn('tmp');

        $app = $app->shouldReceive('make')
            ->with('path')
            ->andReturn('tests');

        $app = $app->mock();

        Facade::setFacadeApplication($app);
    }

    public function tearDown($value='')
    {
        M::close();
    }

    public function testInstance()
    {
        $generator = new LangJsGenerator(new File);
        $command = new LangJsCommand($generator);
    }

    public function testFireCommand()
    {
        $generator = new LangJsGenerator(new File);
        $command = new LangJsCommand($generator);

        $tester = new CommandTester($command);
        $tester->execute(array());
    }
}
