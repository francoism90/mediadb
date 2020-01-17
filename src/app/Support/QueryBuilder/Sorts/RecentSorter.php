<?php

namespace App\Support\QueryBuilder\Sorts;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class RecentSorter implements Sort
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
        return $query->orderBy('created_at', 'desc');
    }
}
