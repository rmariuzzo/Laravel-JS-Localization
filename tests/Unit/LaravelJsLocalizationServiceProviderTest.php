<?php

use Illuminate\Support\Facades\Config;
use Mariuzzo\LaravelJsLocalization\Commands\LangJsCommand;
use Mariuzzo\LaravelJsLocalization\Generators\LangJsGenerator;
use Mariuzzo\LaravelJsLocalization\LaravelJsLocalizationServiceProvider;

it('merges configuration correctly', function () {
    expect(Config::has('localization-js'))->toBeTrue();
});

it('registers the LangJsGenerator service', function () {
    expect(app()->bound(LangJsGenerator::class))->toBeTrue()
        ->and(app(LangJsGenerator::class))->toBeInstanceOf(LangJsGenerator::class);
});

it('registers the command', function () {
    $this->app->singleton(LangJsCommand::class, function ($app) {
        return new LangJsCommand();
    });

    expect(app()->bound(LangJsCommand::class))->toBeTrue()
        ->and(app(LangJsCommand::class))->toBeInstanceOf(LangJsCommand::class);
});

it('provides correct services', function () {
    $provider = app()->getProvider(LaravelJsLocalizationServiceProvider::class);

    expect($provider->provides())->toContain(LangJsCommand::class);
});

it('publishes configuration file', function () {
    $provider = new LaravelJsLocalizationServiceProvider(app());
    $provider->boot();

    expect(LaravelJsLocalizationServiceProvider::$publishGroups)
        ->toHaveKey('localization-js-config');
});
