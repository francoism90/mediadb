<?php

namespace App\Support\Scout\Rules;

use ScoutElastic\SearchRule;

class SimpleMatchRule extends SearchRule
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
                    'fields' => ['name'],
                    'flags' => 'PHRASE|AND|PRECEDENCE|PREFIX|WHITESPACE',
                ],
            ],
        ];
    }
}
