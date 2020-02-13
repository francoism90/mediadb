<?php

namespace App\Support\Scout;

use ScoutElastic\IndexConfigurator;
use ScoutElastic\Migratable;

class MediaIndexConfigurator extends IndexConfigurator
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
                    ],
                ],
                'autocomplete_search' => [
                    'tokenizer' => 'lowercase',
                ],
            ],
            'tokenizer' => [
                'autocomplete' => [
                    'type' => 'edge_ngram',
                    'min_gram' => 2,
                    'max_gram' => 10,
                    'token_chars' => [
                        'letter',
                        'digit',
                    ],
                ],
            ],
        ],
    ];
}
