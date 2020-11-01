<?php

namespace App\Support\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class FavoritedFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $modelClass = get_class($query->getModel());

        $models = auth()->user()->favorites($modelClass)->get();

        $ids = $models->pluck('id')->toArray();
        $idsOrder = implode(',', $ids);

        return $query
            ->whereIn('id', $ids)
            ->orderByRaw("FIELD(id, {$idsOrder})");
    }
}
