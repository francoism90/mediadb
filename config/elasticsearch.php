<?php

return [
    'host' => env('ELASTICSEARCH_HOST'),
    'indices' => [
        'mappings' => [
            'default' => [
                'properties' => [
                    'id' => [
                        'type' => 'keyword',
                    ],
                    'name' => [
                        'type' => 'text',
                        'analyzer' => 'autocomplete',
                        'search_analyzer' => 'autocomplete_search',
                    ],
                ],
            ],
            'videos' => [
                'properties' => [
                    'id' => [
                        'type' => 'keyword',
                    ],
                    'name' => [
                        'type' => 'text',
                        'analyzer' => 'autocomplete',
                        'search_analyzer' => 'autocomplete_search',
                    ],
                    'duration' => [
                        'type' => 'float',
                    ],
                ],
            ],
        ],
        'settings' => [
            'default' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
                'analysis' => [
                    'analyzer' => [
                        'autocomplete' => [
                            'tokenizer' => 'autocomplete',
                            'filter' => [
                                'lowercase',
                                'trim',
                            ],
                        ],
                        'autocomplete_search' => [
                            'type' => 'custom',
                            'tokenizer' => 'whitespace',
                            'filter' => [
                                'lowercase',
                                'ascii_folding',
                            ],
                        ],
                    ],
                    'filter' => [
                        'ascii_folding' => [
                            'type' => 'asciifolding',
                            'preserve_original' => true,
                        ],
                    ],
                    'tokenizer' => [
                        'autocomplete' => [
                            'type' => 'edge_ngram',
                            'min_gram' => 1,
                            'max_gram' => 20,
                            'token_chars' => [
                                'letter',
                                'digit',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
