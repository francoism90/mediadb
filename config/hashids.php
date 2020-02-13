<?php

/*
 * This file is part of Laravel Hashids.
 *
 * (c) Vincent Klaiber <hello@doubledip.se>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use App\Models\Collection;
use App\Models\Media;
use App\Models\Tag;

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
        Collection::class => [
            'salt' => 'zcbi7z6k7nfwiz3eor4749idrkat9d6t',
            'length' => '12',
        ],

        Media::class => [
            'salt' => 'tmuuf2cz3we4agp64fojxbo5vy4nf4tz',
            'length' => '12',
        ],

        Tag::class => [
            'salt' => '2adq6t9z6dzkvrhpwx97hzxo663ri4',
            'length' => '12',
        ],
    ],
];
