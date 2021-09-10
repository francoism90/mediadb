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

        'expires' => env('DASH_EXPIRES', 60 * 24),
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

        'captions_extensions' => [
            '*.vtt',
        ],

        'captions_mimetypes' => [
            'text/plain',
            'text/vtt',
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
