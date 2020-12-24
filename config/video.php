<?php

return [
    /*
    |--------------------------------------------------------------------------
    | VOD Settings
    |--------------------------------------------------------------------------
    |
    | This controls the video-on-demand (VOD) server.
    |
    */

    /*
     * @reminder update the .env file and the streaming server.
    */

    'vod_url' => env('VOD_URL'),

    'vod_key' => env('VOD_KEY'),

    'vod_iv' => env('VOD_IV'),

    'vod_expire' => env('VOD_EXPIRE', 60 * 24),

    /*
    |--------------------------------------------------------------------------
    | Video Import
    |--------------------------------------------------------------------------
    |
    | This controls the video importing process.
    |
    */

    /*
     * @doc https://github.com/kaltura/nginx-vod-module/issues/427
    */

    'accept_mimetypes' => [
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

    /*
    |--------------------------------------------------------------------------
    | Sprite Conversion
    |--------------------------------------------------------------------------
    |
    | This controls the sprite conversion process.
    |
    */

    'sprite_name' => 'sprite.webp',

    'sprite_interval' => 10,

    'sprite_columns' => 20,

    'sprite_width' => 160,

    'sprite_height' => 90,

    'sprite_filter' => 'scale=160:190',

    /*
    |--------------------------------------------------------------------------
    | Thumbnail Conversion
    |--------------------------------------------------------------------------
    |
    | This controls the thumbnail conversion process.
    |
    */

    'thumbnail_name' => 'thumbnail.jpg',

    'thumbnail_filter' => 'scale=2048:-1',
];
