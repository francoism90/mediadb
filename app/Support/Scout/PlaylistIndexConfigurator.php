<?php

namespace App\Support\Scout;

use ScoutElastic\IndexConfigurator;
use ScoutElastic\Migratable;

class PlaylistIndexConfigurator extends IndexConfigurator
{
    use Migratable;

    /**
     * @var array
     */
    protected $settings = [
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
    ];
}
