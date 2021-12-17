<?php

/**
 * Copyright (c) Vincent Klaiber.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://github.com/vinkla/laravel-hashids
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the connections below you wish to use as
    | your default connection for all work. Of course, you may use many
    | connections at once using the manager class.
    |
    */

    'default' => null,

    /*
    |--------------------------------------------------------------------------
    | Hashids Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the connections setup for your application. Example
    | configuration has been included, but you may add as many connections as
    | you would like.
    |
    */

    'connections' => [
        App\Models\Media::class => [
            'salt' => 'q4ahc1xwj6elvkbd4i6qtlc99jjstfme',
            'length' => '12',
        ],

        App\Models\Tag::class => [
            'salt' => 'mwlilfokhxgomwhdllm5ffjrsccwztvd',
            'length' => '12',
        ],

        App\Models\User::class => [
            'salt' => 'kmoiwh9oiqxs1tq6si4xpwkwkimud2qo',
            'length' => '12',
        ],

        App\Models\Video::class => [
            'salt' => 'odaiitbszbcbjs3nhlhhbafkrti77ket',
            'length' => '12',
        ],
    ],
];
