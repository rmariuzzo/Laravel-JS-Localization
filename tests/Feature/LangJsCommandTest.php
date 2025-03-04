<?php

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
