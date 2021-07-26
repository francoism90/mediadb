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

    'vod_url' => env('VOD_URL'),

    'vod_key' => env('VOD_KEY'),

    'vod_iv' => env('VOD_IV'),

    'vod_expires' => env('VOD_EXPIRES', 60 * 24),

    /*
    |--------------------------------------------------------------------------
    | Syncing
    |--------------------------------------------------------------------------
    |
    | This controls media syncing/importing.
    |
    */

    'sync' => [
        'extensions' => [
            '*.mp4',
            '*.m4v',
        ],

        'video_mimetypes' => [
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

        'caption_mimetypes' => [
            'text/plain',
            'text/vtt',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Conversions
    |--------------------------------------------------------------------------
    |
    | This controls conversions.
    |
    */

    'conversions' => [
        'thumbnail' => [
            'path' => 'thumbnail.jpg',
            'filter' => 'scale=2048:-1',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Video Attributes
    |--------------------------------------------------------------------------
    |
    | This controls video attributes.
    |
    */

    'resolutions' => [
        ['width' => 352, 'label' => '240p'],
        ['width' => 480, 'label' => '360p'],
        ['width' => 858, 'label' => '480p'],
        ['width' => 576, 'label' => '576p'],
        ['width' => 640, 'label' => '640p'],
        ['width' => 960, 'label' => '720p'],
        ['width' => 1280, 'label' => '720p'],
        ['width' => 1920, 'label' => '1080p'],
        ['width' => 2560, 'label' => '4K'],
        ['width' => 3840, 'label' => '4K UHD'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tag Attributes
    |--------------------------------------------------------------------------
    |
    | This controls tag attributes.
    |
    */

    'tag_types' => [
        'actor',
        'genre',
        'language',
        'studio',
    ],
];
