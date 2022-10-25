<?php

namespace Mariuzzo\LaravelJsLocalization\Commands;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
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
     * Fire the command. (Compatibility for < 5.0)
     */
    public function fire()
    {
        $this->handle(App::make(LangJsGenerator::class));
    }

    /**
     * Handle the command.
     */
    public function handle(LangJsGenerator $generator)
    {
        $target = $this->argument('target');
        $options = [
            'compress' => $this->option('compress'),
            'json' => $this->option('json'),
            'no-lib' => $this->option('no-lib'),
            'source' => $this->option('source'),
            'no-sort' => $this->option('no-sort'),
        ];

        if ($generator->generate($target, $options)) {
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
            ['target', InputArgument::OPTIONAL, 'Target path.', $this->getDefaultPath()],
        ];
    }

    /**
     * Return the path to use when no path is specified.
     *
     * @return string
     */
    protected function getDefaultPath()
    {
        return Config::get('localization-js.path', public_path('messages.js'));
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
            ['no-lib', 'nl', InputOption::VALUE_NONE, 'Do not include the lang.js library.', null],
            ['json', 'j', InputOption::VALUE_NONE, 'Only output the messages json.', null],
            ['source', 's', InputOption::VALUE_REQUIRED, 'Specifying a custom source folder', null],
            ['no-sort', 'ns', InputOption::VALUE_NONE, 'Do not sort the messages', null],
        ];
    }
}
