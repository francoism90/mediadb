<?php

namespace App\Support\QueryBuilder\Sorters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class RandomSorter implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property): Builder
    {
        $query->getQuery()->reorder();

        return $query->inRandomSeedOrder();
    }
}
