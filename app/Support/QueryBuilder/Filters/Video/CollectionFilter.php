<?php

namespace App\Support\QueryBuilder\Filters\Video;

use App\Models\Collection;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class CollectionFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $value = is_array($value) ? implode(',', $value) : $value;

        $models = Collection::findByHash($value)->videos;

        $ids = $models->pluck('id')->toArray();
        $idsOrder = implode(',', $ids);

        return $query
            ->whereIn('id', $ids)
            ->orderByRaw("FIELD(id, {$idsOrder})");
    }
}
