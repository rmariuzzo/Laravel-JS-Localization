<?php namespace Mariuzzo\LaravelJsLocalization;

use Illuminate\Support\ServiceProvider;

/**
 * The LaravelJsLocalizationServiceProvider class.
 *
 * @author Rubens Mariuzzo <rubens@mariuzzo.com>
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
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['localization.js'] = $this->app->share(function ($app)
        {
            $files = $app['files'];
            $langs = $app['path.base'].'/resources/lang';
            $generator = new Generators\LangJsGenerator($files, $langs);
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
        return array('localization.js');
    }
}
