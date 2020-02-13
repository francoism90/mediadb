<?php

namespace App\Support\Scout\Rules;

use ScoutElastic\SearchRule;

class FuzzyMatchRule extends SearchRule
{
    /**
     * {@inheritdoc}
     */
    public function buildQueryPayload()
    {
        return [
            'must' => [
                'fuzzy' => [
                    'name' => $this->builder->query,
                ],
            ],
        ];
    }
}
