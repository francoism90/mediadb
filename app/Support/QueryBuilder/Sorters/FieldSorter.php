<?php

namespace App\Support\QueryBuilder\Sorters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class FieldSorter implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property): Builder
    {
        $query->getQuery()->reorder();

        $column = sprintf('%s.%s', $query->getQuery()->from, $property);
        $direction = $descending ? 'DESC' : 'ASC';

        return $query->orderBy($column, $direction);
    }
}
