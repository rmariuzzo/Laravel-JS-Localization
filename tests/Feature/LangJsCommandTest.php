<?php

use Illuminate\Support\Facades\Config;

beforeEach(function () {
    $this->testPath       = __DIR__ . '/..';
    $this->rootPath       = __DIR__ . '/../..';
    $this->outputFilePath = "$this->testPath/output/lang.js";
    $this->langPath       = "$this->testPath/fixtures/lang";
});

test('it generates the JS language file successfully', function () {
    $this->artisan('lang:js', ['target' => $this->outputFilePath])
        ->expectsOutputToContain('Created:')
        ->assertExitCode(0);

    $this->assertFileExists($this->outputFilePath);

    $template = "$this->rootPath/src/Generators/Templates/langjs_with_messages.js";
    $this->assertFileExists($template);
    $this->assertFileNotEquals($template, $this->outputFilePath);

    $this->cleanupOutputDirectory($this->testPath);
});

test('template should have handlebars', function () {
    $template = "$this->rootPath/src/Generators/Templates/messages.js";
    $this->assertFileExists($template);

    $contents = file_get_contents($template);
    $this->assertNotEmpty($contents);
    $this->assertHasHandlebars('messages', $contents);
});

test('template messages should have handlebars', function () {
    $template = "$this->rootPath/src/Generators/Templates/langjs_with_messages.js";
    $this->assertFileExists($template);

    $contents = file_get_contents($template);
    $this->assertNotEmpty($contents);
    $this->assertHasHandlebars('messages', $contents);
    $this->assertHasHandlebars('langjs', $contents);
});

test('output should not have handlebars', function () {
    $this->artisan('lang:js', ['target' => $this->outputFilePath])
        ->expectsOutputToContain('Created:')
        ->assertExitCode(0);

    $this->assertFileExists($this->outputFilePath);

    $contents = file_get_contents($this->outputFilePath);
    $this->assertNotEmpty($contents);
    $this->assertHasNotHandlebars('messages', $contents);
    $this->assertHasNotHandlebars('langjs', $contents);

    $this->cleanupOutputDirectory($this->testPath);
});

test('all files should be converted', function () {
    $this->artisan('lang:js', ['target' => $this->outputFilePath, '--source' => $this->langPath])
        ->assertExitCode(0);

    $this->assertFileExists($this->outputFilePath);

    $contents = file_get_contents($this->outputFilePath);

    $this->assertStringContainsString('gm8ft2hrrlq1u6m54we9udi', $contents);

    $this->assertStringNotContainsString('vendor.nonameinc.en.messages', $contents);
    $this->assertStringNotContainsString('vendor.nonameinc.es.messages', $contents);
    $this->assertStringNotContainsString('vendor.nonameinc.ht.messages', $contents);

    $this->assertStringContainsString('en.nonameinc::messages', $contents);
    $this->assertStringContainsString('es.nonameinc::messages', $contents);
    $this->assertStringContainsString('ht.nonameinc::messages', $contents);

    $this->assertStringContainsString('en.forum.thread', $contents);

    $this->cleanupOutputDirectory($this->testPath);
});

test('selected files in config should be converted', function () {
    Config::set('localization-js.messages', ['messages']);

    $this->artisan('lang:js', ['target' => $this->outputFilePath, '--source' => $this->langPath])
        ->assertExitCode(0);

    $this->assertFileExists($this->outputFilePath);

    $contents = file_get_contents($this->outputFilePath);

    $this->assertStringContainsString('en.messages', $contents);
    $this->assertStringNotContainsString('en.validation', $contents);

    $this->cleanupOutputDirectory($this->testPath);
});

test('nested directory files in config should be converted', function () {
    Config::set('localization-js.messages', ['forum/thread']);

    $this->artisan('lang:js', ['target' => $this->outputFilePath, '--source' => $this->langPath])
        ->assertExitCode(0);

    $this->assertFileExists($this->outputFilePath);

    $contents = file_get_contents($this->outputFilePath);

    $this->assertStringContainsString('en.forum.thread', $contents);

    $this->cleanupOutputDirectory($this->testPath);
});

test('should use default output path from config', function () {
    $customOutputFilePath = "{$this->testPath}/output/lang-with-custom-path.js";
    Config::set('localization-js.path', $customOutputFilePath);

    $this->artisan('lang:js')
        ->expectsOutputToContain('Created:')
        ->assertExitCode(0);

    $this->assertFileExists($customOutputFilePath);

    $template = "$this->rootPath/src/Generators/Templates/langjs_with_messages.js";
    $this->assertFileExists($template);
    $this->assertFileNotEquals($template, $customOutputFilePath);

    $this->cleanupOutputDirectory($this->testPath);
});

test('should ignore default output path from config if target argument exists', function () {
    $customOutputFilePath = "{$this->testPath}/output/lang-with-custom-path.js";
    Config::set('localization-js.path', $customOutputFilePath);

    $this->artisan('lang:js', ['target' => $this->outputFilePath])
        ->expectsOutputToContain('Created:')
        ->assertExitCode(0);

    $this->assertFileExists($this->outputFilePath);
    $this->assertFileDoesNotExist($customOutputFilePath);

    $template = "$this->rootPath/src/Generators/Templates/langjs_with_messages.js";
    $this->assertFileExists($template);
    $this->assertFileNotEquals($template, $this->outputFilePath);

    $this->cleanupOutputDirectory($this->testPath);
});

test('only messages should export', function () {
    $this->artisan('lang:js', ['target' => $this->outputFilePath, '--no-lib' => true])
        ->assertExitCode(0);

    $this->assertFileExists($this->outputFilePath);

    $contents = file_get_contents($this->outputFilePath);
    $this->assertNotEmpty($contents);
    $this->assertHasNotHandlebars('messages', $contents);
    $this->cleanupOutputDirectory($this->testPath);
});

test('only messages json should export', function () {
    $this->artisan('lang:js', ['target' => $this->outputFilePath, '--json' => true])
        ->assertExitCode(0);

    $this->assertFileExists($this->outputFilePath);

    $contents = file_get_contents($this->outputFilePath);
    $this->assertNotEmpty($contents);
    $this->assertHasNotHandlebars('messages', $contents);
    $this->cleanupOutputDirectory($this->testPath);
});
