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

    'sync' => [
        'video_extensions' => [
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

        'caption_extensions' => [
            '*.vtt',
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
        'sprite' => [
            'path' => 'sprite.vtt',
            'steps' => 10,
        ],

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
        ['width' => 352, 'label' => '240p', 'icon' => 'sd'],
        ['width' => 480, 'label' => '360p', 'icon' => 'sd'],
        ['width' => 858, 'label' => '480p', 'icon' => 'sd'],
        ['width' => 576, 'label' => '576p', 'icon' => 'sd'],
        ['width' => 640, 'label' => '640p', 'icon' => 'sd'],
        ['width' => 960, 'label' => '720p', 'icon' => 'hd'],
        ['width' => 1280, 'label' => '720p', 'icon' => 'hd'],
        ['width' => 1920, 'label' => '1080p', 'icon' => 'hd'],
        ['width' => 2560, 'label' => '4K', 'icon' => '4k'],
        ['width' => 3840, 'label' => '4K UHD', 'icon' => '4k_plus'],
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
