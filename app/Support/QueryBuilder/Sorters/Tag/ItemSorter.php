<?php

namespace App\Support\QueryBuilder\Sorters\Tag;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class ItemSorter implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property): Builder
    {
        return $query
            ->withCount('videos')
            ->orderByDesc('videos_count');
    }
}
