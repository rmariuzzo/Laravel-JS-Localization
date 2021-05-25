<?php
namespace Mariuzzo\LaravelJsLocalization;


use Illuminate\Support\ServiceProvider;

class NonameIncProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../fixtures/packages/nonameinc/lang', 'nonameinc');
    }

}

