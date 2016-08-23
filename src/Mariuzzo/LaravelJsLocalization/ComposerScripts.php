<?php

namespace Mariuzzo\LaravelJsLocalization;

/**
 * The ComposerScripts class.
 *
 * @package Mariuzzo
 * @author  Rubens Mariuzzo <rubens@mariuzzo.com>
 */
class ComposerScripts
{
    /**
     * Handle the post-install Composer event.
     */
    public static function postInstall()
    {
        exec('git submodule update --init --recursive');
        copy('Lang.js/dist/lang.min.js', 'lib/lang.min.js');
    }

}
