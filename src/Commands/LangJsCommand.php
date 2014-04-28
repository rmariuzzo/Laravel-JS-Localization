<?php namespace Mariuzzo\LaravelJsLocalization\Commands;

use Illuminate\Console\Command;
use Mariuzzo\LaravelJsLocalization\Generators\LangJsGenerator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class LangJsCommand extends Command
{
    protected $name = 'lang:js';

    protected $description = 'Generate JS lang files.';

    protected $generator;

    public function __construct(LangJsGenerator $generator)
    {
        $this->generator = $generator;
        parent::__construct();
    }

    public function fire()
    {
        $target = $this->argument('target');

        if ($this->generator->make($target))
        {
            return $this->info("Created: {$target}");
        }

        $this->error("Could not create: {$target}");
    }

    protected function getArguments()
    {
        return array(
            array('target', InputArgument::OPTIONAL, 'Target path.', public_path() . '/messages.js'),
        );
    }

    protected function getOptions()
    {
        return array();
    }
}
