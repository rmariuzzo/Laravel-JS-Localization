<?php

namespace Mariuzzo\LaravelJsLocalization\Commands;

use Illuminate\Console\Command;
use Mariuzzo\LaravelJsLocalization\Generators\LangJsGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * The LangJsCommand class.
 *
 * @author  Rubens Mariuzzo <rubens@mariuzzo.com>
 */
class LangJsCommand extends Command
{
    /**
     * The command name.
     *
     * @var string
     */
    protected $name = 'lang:js';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Generate JS lang files.';

    /**
     * The generator instance.
     *
     * @var LangJsGenerator
     */
    protected $generator;

    /**
     * Construct a new LangJsCommand.
     *
     * @param LangJsGenerator $generator The generator.
     */
    public function __construct(LangJsGenerator $generator)
    {
        $this->generator = $generator;
        parent::__construct();
    }

    /**
     * Fire the command.
     */
    public function fire()
    {
        $target = $this->argument('target');
        $options = ['compress' => $this->option('compress')];

        if ($this->generator->generate($target, $options)) {
            $this->info("Created: {$target}");

            return;
        }

        $this->error("Could not create: {$target}");
    }

    /**
     * Return all command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['target', InputArgument::OPTIONAL, 'Target path.', $this->getPublicPath().'/messages.js'],
        ];
    }

    /**
     * Return all command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['compress', 'c', InputOption::VALUE_NONE, 'Compress the JavaScript file.', null],
        ];
    }

    /**
     * Return the public path of the Laravel application.
     *
     * @return string
     */
    public function getPublicPath()
    {
        return public_path();
    }
}
