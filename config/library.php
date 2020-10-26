<?php

return [
    /*
    * @doc https://github.com/kaltura/nginx-vod-module/issues/427
    */
    'extensions' => [
        'm4v',
        'mp4',
        'vtt',
        'webp',
        // 'ogm',
        // 'ogv',
        // 'ogx',
        // 'vp8',
        // 'vp9',
        // 'webm',
    ],

    'mimetypes' => [
        'image/webp',
        'text/plain',
        'text/vtt',
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

    'sizes' => [
        '<= 10G',
    ],
];
