<?php

namespace App\Support\QueryBuilder\Sorters\Tag;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class ItemSorter implements Sort
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
        // Removing existing orderings
        $query->getQuery()->reorder();

        return $query
            ->withCount(['collections', 'videos'])
            ->orderBy('collections_count', 'DESC')
            ->orderBy('videos_count', 'DESC');
    }
}
