<?php

namespace App\Support\QueryBuilder\Sorters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class MostViewsSorter implements Sort
{
    /**
     * @param Builder $query
     * @param bool    $descending
     * @param string  $property
     *
     * @return Builder
     */
    public function __invoke(Builder $query, bool $descending, string $property): Builder
    {
        // Removing existing orderings
        $query->getQuery()->reorder();

        return $query->orderByUniqueViews('DESC', null, 'view_count', true);
    }
}
