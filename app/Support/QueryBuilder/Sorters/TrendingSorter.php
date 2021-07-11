<?php

namespace App\Support\QueryBuilder\Sorters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class TrendingSorter implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property): Builder
    {
        return $query->reorder();
    }
}
