<?php

namespace App\Support\QueryBuilder\Sorters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Spatie\QueryBuilder\Sorts\Sort;

class TrendingSorter implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property): Builder
    {
        return $query
            ->with('viewers')
            ->withCount(['viewers' => function (Builder $query) {
                $query->where('interactions.created_at', '>=', Carbon::now()->subHours(24));
            }])
            ->orderByDesc('viewers_count');
    }
}
