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
            'name' => 'tags_index',
            'settings' => [
                'searchableAttributes' => [
                    'name',
                    'description',
                    'type',
                ],
                'filterableAttributes' => [
                    'id',
                    'uuid',
                    'slug',
                ],
                'sortableAttributes' => [
                    'name',
                    'description',
                    'type',
                ],
            ],
        ],
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
                    'uuid',
                ],
                'sortableAttributes' => [
                    'name',
                    'email',
                    'description',
                ],
            ],
        ],
        [
            'name' => 'videos_index',
            'settings' => [
                'rankingRules' => [
                    'sort',
                    'created:asc',
                    'id:desc',
                    'words',
                    'typo',
                    'proximity',
                    'attribute',
                    'exactness',
                ],
                'searchableAttributes' => [
                    'name',
                    'season_number',
                    'episode_number',
                    'overview',
                    'actors',
                    'studios',
                    'genres',
                    'languages',
                    'tags',
                    'descriptions',
                ],
                'filterableAttributes' => [
                    'id',
                    'uuid',
                    'actors',
                    'studios',
                    'genres',
                    'languages',
                    'tags',
                ],
                'sortableAttributes' => [
                    'name',
                    'season_number',
                    'episode_number',
                    'duration',
                    'overview',
                    'created',
                    'updated',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | MeiliSearch Synonyms
    |--------------------------------------------------------------------------
    |
    | Synonyms are considered to be the same.
    | A search on a word or its synonym will return the same search result.
    |
    */

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

    /*
    |--------------------------------------------------------------------------
    | MeiliSearch Stop Words
    |--------------------------------------------------------------------------
    |
    | The stop-words route allows you to add a list of words ignored in your search queries.
    | Adding a stop-words list improves the speed, and relevancy of a search.
    |
    */

    'stop_words' => [
        '.',
        ',',
        '-',
        '_',
        '|',
        '(',
        ')',
        '&',
        'a',
        'and',
        'or',
        'the',
    ],
];
