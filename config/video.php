<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Video Import
    |--------------------------------------------------------------------------
    |
    | This controls the video importing process.
    | @doc https://github.com/kaltura/nginx-vod-module/issues/427
    |
    */

    'clip_mimetypes' => [
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

    /*
    |--------------------------------------------------------------------------
    | Video Conversions
    |--------------------------------------------------------------------------
    |
    | This controls media conversions.
    |
    */

    'conversions' => [
        'thumbnail',
    ],
];
