<?php

return [
    /*
    |--------------------------------------------------------------------------
    | VOD URL
    |--------------------------------------------------------------------------
    |
    | The URL to the main streaming server.
    |
    */

    'url' => env('VOD_URL'),

    /*
    |--------------------------------------------------------------------------
    | VOD Secrets
    |--------------------------------------------------------------------------
    |
    | @reminder update the .env file and the streaming server.
    |
    */

    'key' => env('VOD_KEY'),

    'iv' => env('VOD_IV'),

    'secret' => env('VOD_SECRET'),

    'expire' => env('VOD_EXPIRE', 60 * 60 * 8),
];
