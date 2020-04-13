<?php

return [

    /*
     * Set the names of files you want to add to generated javascript.
     * Otherwise all the files will be included.
     *
     * 'messages' => [
     *     'validation',
     *     'forum/thread',
     * ],
     */
    'messages' => [

    ],

    /*
     * The default path to use for the generated javascript.
     */
    'path' => public_path('messages.js'),

    /*
     * Specify the files you want the autodetector to search through.
     * Use glob format.
     *
     * 'usageSearchFiles' => [
     *     'validation',
     *     'forum/thread',
     * ],
     */
    'usageSearchFiles' => [
        'public/**/.js',
        'resources/assets/**/*.js',
        'resources/views/**/*',
    ],
];
