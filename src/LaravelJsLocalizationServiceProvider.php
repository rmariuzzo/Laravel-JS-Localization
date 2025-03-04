<?php

namespace Mariuzzo\LaravelJsLocalization;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Mariuzzo\LaravelJsLocalization\Commands\LangJsCommand;
use Mariuzzo\LaravelJsLocalization\Generators\LangJsGenerator;

/**
 * The LaravelJsLocalizationServiceProvider class.
 *
 * @author  Rubens Mariuzzo <rubens@mariuzzo.com>
 */
class LaravelJsLocalizationServiceProvider extends ServiceProvider implements DeferrableProvider {
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     */
    public function boot() {
        if ($this->app->runningInConsole()) {
            $this->registerCommands();
            $this->publishConfigs();
        }
    }

    /**
     * Register the service provider.
     */
    public function register() {
        $this->registerConfig();
        $this->registerLangJsGenerator();
    }

    protected function registerConfig(): void {
        $this->mergeConfigFrom(__DIR__ . '/../config/localization-js.php', 'localization-js');
    }

    protected function registerCommands(): void {
        $this->commands([
            LangJsCommand::class,
        ]);
    }

    protected function publishConfigs(): void {
        $this->publishes([
            __DIR__ . '/../config/localization-js.php' => config_path('localization-js.php'),
        ], 'localization-js-config');
    }

    protected function registerLangJsGenerator(): void {
        $this->app->singleton(LangJsGenerator::class, function ($app) {
            $app = $this->app;

            $files = $app['files'];
            $langs = $app['path.base'] . '/lang';

            $messages = $app['config']->get('localization-js.messages');
            return new LangJsGenerator($files, $langs, $messages);
        });
    }

    /**
     * Get the services provided by this provider.
     *
     * @return array
     */
    public function provides() {
        return [LangJsCommand::class];
    }
}
