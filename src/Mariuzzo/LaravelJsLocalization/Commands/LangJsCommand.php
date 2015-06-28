<?php namespace Mariuzzo\LaravelJsLocalization\Commands;

use Illuminate\Console\Command;
use Mariuzzo\LaravelJsLocalization\Generators\LangJsGenerator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * The LangJsCommand class.
 *
 * @author Rubens Mariuzzo <rubens@mariuzzo.com>
 */
class LangJsCommand extends Command
{
    /**
     * The command name.
     */
    protected $name = 'lang:js';

    /**
     * The command description.
     */
    protected $description = 'Generate JS lang files.';

    /**
     * The generator instance.
     */
    protected $generator;

    /**
     * Construct a new LangJsCommand.
     *
     * @param Mariuzzo\LaravelJsLocalization\Genrators\LangJsGenerator The generator instance.
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

        if ($this->generator->generate($target, $options))
        {
            return $this->info("Created: {$target}");
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
            ['target', InputArgument::OPTIONAL, 'Target path.', $this->getPublicPath() . '/messages.js'],
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
