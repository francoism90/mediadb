<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Media Streaming
    |--------------------------------------------------------------------------
    |
    | This controls media streaming.
    |
    */

    'vod_url' => env('VOD_URL'),

    'vod_key' => env('VOD_KEY'),

    'vod_iv' => env('VOD_IV'),

    'vod_expire' => env('VOD_EXPIRE', 60 * 24),

    /*
    |--------------------------------------------------------------------------
    | Media Sync
    |--------------------------------------------------------------------------
    |
    | This controls media syncing.
    |
    */

    'importable' => [
        '*.mp4',
        '*.m4v',
    ],

    /*
    |--------------------------------------------------------------------------
    | Media Conversions
    |--------------------------------------------------------------------------
    |
    | This controls media conversions.
    |
    */

    'thumbnail_name' => 'thumbnail.jpg',

    'thumbnail_filter' => 'scale=2048:-1',

    /*
    |--------------------------------------------------------------------------
    | Media Attributes
    |--------------------------------------------------------------------------
    |
    | This controls media attributes.
    |
    */

    'filter_durations' => [0, 10, 20, 30, 40],

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
];
