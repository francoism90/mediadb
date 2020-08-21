<?php

namespace App\Support\QueryBuilder\Filters\Collection;

use App\Models\Video;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class VideoFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $value = is_array($value) ? implode(' ', $value) : $value;

        $models = Video::findByHash($value)->collections;

        $ids = $models->pluck('id')->toArray();
        $idsOrder = implode(',', $ids);

        return $query->whereIn('id', $ids)
                     ->orderByRaw("FIELD(id, {$idsOrder})");
    }
}
