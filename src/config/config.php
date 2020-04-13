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
     * The autodetector looks for Lang.get / Lang.has / Lang.choice usages in your Javascript files.
     * Use glob format. https://en.wikipedia.org/wiki/Glob_(programming)
     */
    'usageSearchFiles' => [
        'public/**/.js',
        'resources/assets/**/*.js',
        'resources/views/**/*',
    ],
];
