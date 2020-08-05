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

use App\Models\Channel;
use App\Models\Collection;
use App\Models\Media;
use App\Models\Tag;
use App\Models\User;

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
        Channel::class => [
            'salt' => 'op94ywdruie8o5ctndk8pvnfxfm435ma',
            'length' => '12',
        ],

        Collection::class => [
            'salt' => '46ej7d955gfew3ss4avt97hmohovaamh',
            'length' => '12',
        ],

        Media::class => [
            'salt' => 'yk53ywzm5bfhxcychc3w65eyd3hvdvvr',
            'length' => '12',
        ],

        Tag::class => [
            'salt' => 'bjcgme4mqsqu73qufnn9cd5penuaqkc2',
            'length' => '12',
        ],

        User::class => [
            'salt' => 'g5vm89r3vitip345gnesugim8vk5zxrw',
            'length' => '12',
        ],
    ],
];
