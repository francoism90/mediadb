<?php

namespace App\Support\QueryBuilder\Sorters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class MostViewsSorter implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property): Builder
    {
        return $query
            ->with('viewers')
            ->withCount('viewers')
            ->orderByDesc('viewers_count');
    }
}
