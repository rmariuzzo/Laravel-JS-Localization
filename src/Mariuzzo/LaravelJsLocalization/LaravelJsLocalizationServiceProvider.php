<?php

namespace Mariuzzo\LaravelJsLocalization;

use Illuminate\Support\ServiceProvider;

/**
 * The LaravelJsLocalizationServiceProvider class.
 *
 * @author  Rubens Mariuzzo <rubens@mariuzzo.com>
 */
class LaravelJsLocalizationServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        // Publish config files
        $this->publishes([
            __DIR__.'/../../config/config.php' => config_path('localization-js.php'),
        ]);

        $this->mergeConfigFrom(
            __DIR__.'/../../config/config.php', 'localization-js'
        );
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app['localization.js'] = $this->app->share(function ($app) {
            $files = $app['files'];
            $langs = $app['path.base'].'/resources/lang';
            $messages = $app['config']->get('localization-js.messages');
            $generator = new Generators\LangJsGenerator($files, $langs, $messages);

            return new Commands\LangJsCommand($generator);
        });

        $this->commands('localization.js');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['localization.js'];
    }
}
