<?php

namespace App\Support\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class TagTypeFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        // Convert arrays to string
        $value = is_array($value) ? implode(' ', $value) : $value;

        return $query->where('type', $value);
    }
}
