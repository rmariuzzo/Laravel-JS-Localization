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
     * Set the name(s) a file must contain, for it to be included on the generated javascript.
     * E.g. if you place all language files for JS in a separate folder: lang/en/js/example.php, you can use:
     *
     * 'pathContains' => [
            'js'
     * ],
     * Note: this method will check the occurrance of the name, in the full file path (including the file name, but not extension)
     */
    'pathContains' => [

    ],

    /*
     * The default path to use for the generated javascript.
     */
    'path' => public_path('messages.js'),
];
