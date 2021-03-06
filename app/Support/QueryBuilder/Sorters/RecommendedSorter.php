<?php

namespace App\Support\QueryBuilder\Sorters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class RecommendedSorter implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property): Builder
    {
        if ($query->getQuery()->orders) {
            return $query;
        }

        return $query->inRandomSeedOrder();
    }
}
