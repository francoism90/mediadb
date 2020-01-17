<?php

namespace App\Support\QueryBuilder\Sorts;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class RecommendedSorter implements Sort
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed|null                            $descending
     * @param string|null                           $property
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function __invoke(Builder $query, $descending = null, string $property = null): Builder
    {
        return $query->inRandomOrder(
            $query->getModel()->getRandomSeed()
        );
    }
}
