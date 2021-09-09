<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Video-On-Demand
    |--------------------------------------------------------------------------
    |
    | This controls vide-on-demand (VOD).
    |
    */

    'vod' => [
        'url' => env('VOD_URL'),

        'key' => env('VOD_KEY'),

        'iv' => env('VOD_IV'),

        'expires' => env('VOD_EXPIRES', 60 * 24),
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
