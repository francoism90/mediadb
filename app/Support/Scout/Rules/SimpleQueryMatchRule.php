<?php

namespace App\Support\Scout\Rules;

use ScoutElastic\SearchRule;

class SimpleQueryMatchRule extends SearchRule
{
    /**
     * {@inheritdoc}
     */
    public function buildHighlightPayload()
    {
        return [
            'fields' => [
                'name' => [
                    'type' => 'plain',
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildQueryPayload()
    {
        return [
            'must' => [
                'simple_query_string' => [
                    'query' => $this->builder->query,
                    'fields' => ['name^5', 'description'],
                    'flags' => 'PHRASE|AND|PRECEDENCE|PREFIX|WHITESPACE',
                ],
            ],
        ];
    }
}
