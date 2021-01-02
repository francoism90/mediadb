<?php

namespace App\Support\QueryBuilder\Filters\Tag;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class TypeFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $types = is_string($value) ? explode(',', $value) : $value;

        $types = collect($value);

        $isValidType = $types->contains(config('tag.types'));

        return $query
            ->when($isValidType, fn ($query) => $query->whereIn('type', $types->toArray()));
    }
}
