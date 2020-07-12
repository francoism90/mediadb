<?php

return [
    /*
    |--------------------------------------------------------------------------
    | VOD URL
    |--------------------------------------------------------------------------
    |
    | The URL used for connecting to the main streaming server.
    |
    */

    'url' => env('VOD_URL'),

    /*
    |--------------------------------------------------------------------------
    | VOD Secrets
    |--------------------------------------------------------------------------
    |
    | Don't forget to set these in your .env file and be aware that they must
    | match on the streaming server(s).
    |
    */

    'key' => env('VOD_KEY'),

    'iv' => env('VOD_IV'),

    'secret' => env('VOD_SECRET'),

    'expire' => env('VOD_EXPIRE', 60 * 60 * 8),

    /*
    |--------------------------------------------------------------------------
    | VOD Importing
    |--------------------------------------------------------------------------
    |
    | Formats allowed to be imported and used for VOD streaming.
    |
    */

    'import_limit' => env('VOD_IMPORT_LIMIT', 5),

    'extensions' => [
        'm4v',
        'mp4',
        'ogm',
        'ogv',
        'ogx',
        'vp8',
        'vp9',
        'webm',
    ],

    'mimetypes' => [
        'video/mp4',
        'video/mp4v-es',
        'video/ogg',
        'video/vp8',
        'video/vp9',
        'video/webm',
        'video/x-m4v',
        'video/x-ogg',
        'video/x-ogm',
        'video/x-ogm+ogg',
        'video/x-theora',
        'video/x-theora+ogg',
    ],
];
