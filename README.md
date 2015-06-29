Laravel JS Localization
=======================

> Laravel Localization in JavaScript.

![Laravel 5.1](https://img.shields.io/badge/Laravel-5.1-f4645f.svg)
![Laravel 5.0](https://img.shields.io/badge/Laravel-5.0-f4645f.svg)
![Laravel 4.2](https://img.shields.io/badge/Laravel-4.2-f4645f.svg)
[![Latest Stable Version](https://poser.pugx.org/mariuzzo/laravel-js-localization/v/stable.svg)](https://packagist.org/packages/mariuzzo/laravel-js-localization)
[![Total Downloads](https://poser.pugx.org/mariuzzo/laravel-js-localization/downloads.svg)](https://packagist.org/packages/mariuzzo/laravel-js-localization)
[![License](https://poser.pugx.org/mariuzzo/laravel-js-localization/license.svg)](https://packagist.org/packages/mariuzzo/laravel-js-localization)

This is a simple package that convert all your localization messages of your Laravel app to JavaScript, and provides a small JavaScript library to interact with those messages.

Support Laravel 4.2.x, Laravel 5 and Laravel 5.1.x.

Installation
------------

Add the following line to you `composer.json` file under `require`.

    "mariuzzo/laravel-js-localization": "1.2.1"

Run:

    composer update

In your Laravel app go to `app/config/app.php` and add the following service provider:

    'providers' => array(
        ...
        'Mariuzzo\LaravelJsLocalization\LaravelJsLocalizationServiceProvider'
        ...
    )

That's it!

Usage
-----

This project comes with a command that generate the JavaScript version of all your messages found at: `app/lang` or `resources/lang` directory. The resulting JavaScript file will have the whole bunch of messages and a thin library similar to Laravel's `Lang` class.

**Generating JS messages**

    php artisan lang:js

**Specifying a custom target**

    php artisan lang:js public/assets/dist/lang.dist.js

**Compressing the JS file**

    php artisan lang:js -c

Documentation
-------------

This is the documentation regarding the thin JavaScript library. The library highly inspired on Laravel's `Lang` class.

**Getting a message**

    Lang.get('messages.home');

**Getting a message with replacements**

    Lang.get('messages.welcome', { name: 'Joe' });

**Changing the locale**

    Lang.setLocale('es');

**Checking if a message key exists**

    Lang.has('messages.foo');

**Support for singular and plural message based on a count**

    Lang.choice('messages.apples', 10);

**Calling the `choice` method with replacements**

    Lang.choice('messages.apples', 10, { name: 'Joe' });

For more detailed information, take a look at the source: [Lang.js](https://github.com/rmariuzzo/Laravel-JS-Localization/blob/develop/js/lang.js).

Want to contribute?
===================

 1. Fork this repository and clone it.
 2. Create a branch from develop: `git checkout -b feature-foo`.
 3. Push your commits and create a pull request.

Setting up development environment
----------------------------------

**Prerequisites:**

You need to have installed the following softwares.

 - Composer.
 - NodeJS.
 - NPM.
 - PHP 5.4+.

After getting all the required softwares you may run the following commands to get everything ready:

 1. Install PHP dependencies:

    ```shell
    composer install
    ```

 2. Install NPM dependences:

    ```shell
    npm install
    ```

Now you are good to go! Happy coding!

Unit testing
------------

This project use Node-Jasmine and PHPUnit. All tests are stored at `tests` directory.

To run all JS tests type in you terminal:

```shell
npm test
```

To run all PHP tests type in your terminal:

```shell
phpunit
```
