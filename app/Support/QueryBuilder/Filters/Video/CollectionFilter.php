<?php

namespace App\Support\QueryBuilder\Filters\Video;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class CollectionFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $value = is_string($value) ? explode(',', $value) : $value;

        return $query->whereHas('collections', function (Builder $query) use ($value) {
            $query->whereHashids($value);
        });
    }
}
