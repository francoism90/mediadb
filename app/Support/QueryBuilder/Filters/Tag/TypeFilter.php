<?php

namespace App\Support\QueryBuilder\Filters\Tag;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class TypeFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $value = is_string($value) ? explode(' ', $value) : $value;

        return $query->whereIn('type', $value);
    }
}
