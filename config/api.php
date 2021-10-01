<?php

return [
    /*
    |--------------------------------------------------------------------------
    | DASH
    |--------------------------------------------------------------------------
    |
    | This controls dash attributes.
    |
    */

    'dash' => [
        'url' => env('DASH_URL'),

        'key' => env('DASH_KEY'),

        'iv' => env('DASH_IV'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Syncing
    |--------------------------------------------------------------------------
    |
    | This controls media syncing/importing.
    |
    */

    'video' => [
        'clips_extensions' => [
            '*.mp4',
            '*.m4v',
        ],

        'clips_mimetypes' => [
            'video/mp4',
            'video/mp4v-es',
            'video/quicktime',
            'video/x-m4v',
            // 'video/ogg',
            // 'video/vp8',
            // 'video/vp9',
            // 'video/webm',
            // 'video/x-ogg',
            // 'video/x-ogm',
            // 'video/x-ogm+ogg',
            // 'video/x-theora',
            // 'video/x-theora+ogg',
        ],

        'captions_extensions' => [
            '*.vtt',
        ],

        'captions_mimetypes' => [
            'text/plain',
            'text/vtt',
        ],

        'resolutions' => [
            ['name' => '240p', 'width' => 426, 'height' => 240],
            ['name' => '360p', 'width' => 640, 'height' => 360],
            ['name' => '480p', 'width' => 854, 'height' => 480],
            ['name' => '720p', 'width' => 1280, 'height' => 720],
            ['name' => '1080p', 'width' => 1920, 'height' => 1080],
            ['name' => '1440p', 'width' => 2560, 'height' => 1440],
            ['name' => '2160p', 'width' => 3840, 'height' => 2160],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tag Attributes
    |--------------------------------------------------------------------------
    |
    | This controls tag attributes.
    |
    */

    'tag' => [
        'types' => [
            'actor',
            'genre',
            'language',
            'studio',
        ],
    ],
];
