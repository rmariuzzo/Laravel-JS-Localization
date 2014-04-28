<?php namespace Mariuzzo\LaravelJsLocalization;

use Illuminate\Support\ServiceProvider;

class LaravelJsLocalizationServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function register()
    {
        $this->app['localization.js'] = $this->app->share(function ($app) {
            $generator = new Generators\LangJsGenerator($app['files']);
            return new Commands\LangJsCommand($generator);
        });

        $this->commands('localization.js');
    }

    public function provides()
    {
        return array('localization.js');
    }
}
