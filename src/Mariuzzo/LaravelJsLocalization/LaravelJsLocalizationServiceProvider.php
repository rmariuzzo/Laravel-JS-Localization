<?php

namespace Mariuzzo\LaravelJsLocalization;

use Illuminate\Contracts\Foundation\Application;
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
     * The path of this package configuration file.
     *
     * @var string
     */
    protected $configPath;

    public function __constructor()
    {
        parent::__constructor();
        $this->configPath = $this->app['path.config'].DIRECTORY_SEPARATOR;
    }

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $configPath = __DIR__.'/../../config/config.php';
        $configKey = 'localization-js';

        // Determines Laravel major version.
        $app = $this->app;
        $laravelMajorVersion = (int) $app::VERSION;

        // Publishes Laravel-JS-Localization package files and merge user and
        // package configurations.
        if ($laravelMajorVersion === 4) {
            $config = $this->app['config']->get($configKey, []);
            $this->app['config']->set($configKey, array_merge(require $configPath, $config));
        } elseif ($laravelMajorVersion >= 5) {
            $this->publishes([
                $configPath => config_path("$configKey.php"),
            ]);
            $this->mergeConfigFrom(
                $configPath, $configKey
            );
        }
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        // Bind the Laravel JS Localization Generator into the app IOC.
        $this->app->when(Generators\LangJsGenerator::class)->needs('$sourcePath')
            ->give(static function(Application $app) {
                return $app::VERSION < 5 ? base_path('/app/lang') : base_path('/resources/lang');
            });
        $this->app->when(Generators\LangJsGenerator::class)->needs('$messagesIncluded')
            ->giveConfig('localization-js.messages');

        // Bind the Laravel JS Localization command into Laravel Artisan.
        $this->commands(Commands\LangJsCommand::class);
    }

    /**
     * Get the services provided by this provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['localization.js'];
    }
}
