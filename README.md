# ![Laravel JS Localization - Convert you Laravel messages and use them in the front-end!](.github/assets/banner.svg)

[![Latest Stable Version](https://poser.pugx.org/mariuzzo/laravel-js-localization/v/stable.svg)](https://packagist.org/packages/mariuzzo/laravel-js-localization)
[![Total Downloads](https://poser.pugx.org/mariuzzo/laravel-js-localization/downloads.svg)](https://packagist.org/packages/mariuzzo/laravel-js-localization)
[![License](https://poser.pugx.org/mariuzzo/laravel-js-localization/license.svg)](https://packagist.org/packages/mariuzzo/laravel-js-localization)

This package convert all your localization messages from your Laravel app to JavaScript with a small library to interact with those messages following a very similar syntax you are familiar with.

## Features

 - Support Laravel 11.x and 12.x!
 - Includes [Lang.js](https://github.com/rmariuzzo/lang.js)
 - Lang.js API is based on Laravel's [`Translator`](https://api.laravel.com/docs/master/Illuminate/Translation/Translator.html) class. No need to learn a whole API.
 - Allow to specify desired lang files to be converted to JS.

<table><tbody><tr><td>

:star: **Webpack user?** Try the new and shiny [**Laravel localization loader**](https://github.com/rmariuzzo/laravel-localization-loader) for Webpack!

</td></tr></tbody></table>


## Installation

```shell
composer require mariuzzo/laravel-js-localization
```

## Usage

The `Laravel-JS-Localization` package provides a command that generate the JavaScript version of all your messages found at: `lang` directory. The resulting JavaScript file will contain all your messages plus [Lang.js](https://github.com/rmariuzzo/lang.js) (a thin library highly inspired on Laravel's [`Translator`](https://api.laravel.com/docs/master/Illuminate/Translation/Translator.html) class).

### Generating JS messages

```shell
php artisan lang:js
```

### Specifying a custom target

```shell
php artisan lang:js public/assets/dist/lang.dist.js
```

### Compressing the JS file

```shell
php artisan lang:js -c
```

### Specifying a custom source folder

```shell
php artisan lang:js public/assets/dist/lang.dist.js -s themes/default/lang
```

### Output a JSON file instead.

```shell
php artisan lang:js --json
```

## Configuration

First, publish the default package's configuration file running:

```shell
php artisan vendor:publish --provider="Mariuzzo\LaravelJsLocalization\LaravelJsLocalizationServiceProvider"
```

The configuration will be published to `config/localization-js.php`.

You may edit this file to define the messages you need in your Javascript code. Just edit the `messages` array in the config file. **Empty messages array will include all the language files in build**.

To make only `pagination.php` and `validation.php` files to be included in build process:

```php
<?php

return [
    'messages' => [
        'pagination',
        'validation',
    ],
];
```

## Documentation

This is a quick documentation regarding [Lang.js](https://github.com/rmariuzzo/lang.js) (the thin JavaScript library included by `Laravel-JS-Localization`). The [Lang.js](https://github.com/rmariuzzo/lang.js) (a thin library highly inspired on Laravel's [`Translator`](https://api.laravel.com/docs/master/Illuminate/Translation/Translator.html) class).

 > 💁 Go to [Lang.js documentation]([Lang.js](https://github.com/rmariuzzo/lang.js)) to see all available methods.

### Getting a message

```js
Lang.get('messages.home');
```

### Getting a message with replacements

```js
Lang.get('messages.welcome', { name: 'Joe' });
```

### Changing the locale

```js
Lang.setLocale('es');
```

### Checking if a message key exists

```js
Lang.has('messages.foo');
```

### Support for singular and plural message based on a count

```js
Lang.choice('messages.apples', 10);
```

### Calling the `choice` method with replacements

```js
Lang.choice('messages.apples', 10, { name: 'Joe' });
```

> 💁 Go to [Lang.js documentation]([Lang.js](https://github.com/rmariuzzo/lang.js)) to see all available methods.

## Want to contribute?

 1. Fork this repository and clone it.
 2. Create a [feature branch](https://guides.github.com/introduction/flow/) from develop: `git checkout develop; git checkout -b feature-foo`.
 3. Push your commits and create a pull request.

### Prerequisites:

You will need to have installed the following softwares.

 - Composer.
 - PHP 8.2+.

### Development setup

After getting all the required softwares you may run the following commands to get everything ready:

 1. Install PHP dependencies:
    ```shell
    composer install
    ```

 2. Install test dependencies:
    ```shell
    composer test-install
    ```

Now you are good to go! Happy coding!

## Testing

This project uses PHPUnit. All tests are stored at `tests` directory. To run all tests type in your terminal:

```shell
composer test
```

<div align=center>

Made with :heart: by [Rubens Mariuzzo](https://github.com/rmariuzzo).

[MIT license](LICENSE)

</div>
