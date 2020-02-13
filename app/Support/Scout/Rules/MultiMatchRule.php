<?php

namespace App\Support\Scout\Rules;

use ScoutElastic\SearchRule;

class MultiMatchRule extends SearchRule
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
                'multi_match' => [
                    'query' => $this->builder->query,
                    'fields' => ['name^5', 'description'],
                    'tie_breaker' => 0.3,
                ],
            ],
        ];
    }
}
