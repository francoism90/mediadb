<?php

namespace App\Support\QueryBuilder\Sorters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class FieldSorter implements Sort
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param bool                                  $descending
     * @param string                                $property
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function __invoke(Builder $query, bool $descending, string $property): Builder
    {
        $query->getQuery()->orders = null;

        $direction = $descending ? 'DESC' : 'ASC';

        return $query->orderBy($property, $direction);
    }
}
