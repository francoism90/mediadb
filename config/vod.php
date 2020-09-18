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

    /*
    |--------------------------------------------------------------------------
    | VOD Settings
    |--------------------------------------------------------------------------
    |
    | Global settings for imports and streaming.
    |
    | @doc https://github.com/kaltura/nginx-vod-module/issues/427
    |
    */

    'video' => [
        'extensions' => [
            'm4v',
            'mp4',
            // 'ogm',
            // 'ogv',
            // 'ogx',
            // 'vp8',
            // 'vp9',
            // 'webm',
        ],

        'mimetypes' => [
            'video/mp4',
            'video/mp4v-es',
            // 'video/ogg',
            // 'video/vp8',
            // 'video/vp9',
            // 'video/webm',
            'video/x-m4v',
            // 'video/x-ogg',
            // 'video/x-ogm',
            // 'video/x-ogm+ogg',
            // 'video/x-theora',
            // 'video/x-theora+ogg',
        ],

        'size' => ['>= 1M', '<= 10G'],
    ],

    'tracks' => [
        'extensions' => [
            'vtt',
        ],

        'mimetypes' => [
            'text/plain',
            'text/vtt',
        ],

        'languages' => [
            'en' => 'English',
            'nl' => 'Dutch',
        ],

        'size' => ['<= 10M'],

        'types' => [
            'subtitles',
        ],
    ],
];
