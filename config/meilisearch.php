<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MeiliSearch Host Address
    |--------------------------------------------------------------------------
    |
    | This value is used to connect to your MeiliSearch instance. It should
    | include the HTTP address and binding that the server is listening on.
    |
    | For more information on the host address, check out the MeiliSearch
    | documentation here:
    | https://docs.meilisearch.com/guides/advanced_guides/configuration.html
    |
    */

    'host' => env('MEILISEARCH_HOST', 'http://localhost:7700'),

    /*
    |--------------------------------------------------------------------------
    | MeiliSearch Master Key
    |--------------------------------------------------------------------------
    |
    | This value is used to authenticate with your MeiliSearch instance. During
    | development this is not required, but it MUST be set during a production
    | environment.
    |
    | For more information on the master key, check out the MeiliSearch
    | documentation here:
    | https://docs.meilisearch.com/guides/advanced_guides/configuration.html
    |
    */

    'key' => env('MEILISEARCH_KEY', null),

    /*
    |--------------------------------------------------------------------------
    | MeiliSearch Indexes
    |--------------------------------------------------------------------------
    |
    | Used to create and set settings for indexes.
    |
    */

    'indexes' => [
        [
            'name' => 'users_index',
            'settings' => [
                'searchableAttributes' => [
                    'name',
                    'email',
                    'description',
                ],

                'filterableAttributes' => [
                    'id',
                    'created',
                    'updated',
                ],

                'sortableAttributes' => [
                    'name',
                    'email',
                    'description',
                    'created',
                    'updated',
                ],
            ],
        ],
        [
            'name' => 'tags_index',
            'settings' => [
                'searchableAttributes' => [
                    'name',
                    'description',
                    'type',
                ],

                'filterableAttributes' => [
                    'id',
                    'slug',
                    'items',
                    'type',
                    'created',
                    'updated',
                ],

                'sortableAttributes' => [
                    'name',
                    'description',
                    'items',
                    'order',
                    'type',
                    'created',
                    'updated',
                ],
            ],
        ],
        [
            'name' => 'videos_index',
            'settings' => [
                'searchableAttributes' => [
                    'name',
                    'title',
                    'overview',
                    'production_code',
                    'season_number',
                    'episode_number',
                    'actors',
                    'studios',
                    'genres',
                    'languages',
                    'tags',
                    'descriptions',
                ],

                'filterableAttributes' => [
                    'id',
                    'production_code',
                    'season_number',
                    'episode_number',
                    'duration',
                    'features',
                    'resolution',
                    'actors',
                    'studios',
                    'genres',
                    'languages',
                    'tags',
                    'released',
                    'created',
                    'updated',
                ],

                'sortableAttributes' => [
                    'name',
                    'title',
                    'production_code',
                    'season_number',
                    'episode_number',
                    'duration',
                    'views',
                    'released',
                    'created',
                    'updated',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | MeiliSearch Settings
    |--------------------------------------------------------------------------
    |
    | Settings that are being applied to all indexes.
    |
    */

    'settings' => [
        'displayedAttributes' => [
            'id',
            'uuid',
            'slug',
            'name',
        ],

        'synonyms' => [
            '1' => ['01'],
            '2' => ['02'],
            '3' => ['03'],
            '4' => ['04'],
            '5' => ['05'],
            '6' => ['06'],
            '7' => ['07'],
            '8' => ['08'],
            '9' => ['09'],
            '01' => ['1'],
            '02' => ['2'],
            '03' => ['3'],
            '04' => ['4'],
            '05' => ['5'],
            '06' => ['6'],
            '07' => ['7'],
            '08' => ['8'],
            '09' => ['9'],
            '&' => ['and'],
            'and' => ['&'],
            '@' => ['at'],
            'at' => ['@'],
            '#' => ['hash', 'hashtag'],
        ],

        'stopWords' => [
            '.',
            ',',
            '-',
            '_',
            '|',
            '(',
            ')',
            '[',
            ']',
            '&',
            'a',
            'and',
            'or',
            'the',
        ],
    ],
];
