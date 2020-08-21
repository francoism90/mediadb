<?php

namespace App\Support\QueryBuilder\Sorts\Tag;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class ItemsSorter implements Sort
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

        return $query
            ->withCount(['collections', 'videos'])
            ->orderBy('collections_count', 'DESC')
            ->orderBy('videos_count', 'DESC');
    }
}
