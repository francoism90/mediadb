<?php

namespace App\Support\QueryBuilder\Sorters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class FieldSorter implements Sort
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

        $column = "{$query->getQuery()->from}.{$property}";
        $direction = $descending ? 'DESC' : 'ASC';

        return $query
            ->orderBy($column, $direction);
    }
}
