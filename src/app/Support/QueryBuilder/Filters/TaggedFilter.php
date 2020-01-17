<?php

namespace App\Support\QueryBuilder\Filters;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class TaggedFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $tags = Tag::getModelsByKey((array) $value);

        return $query->withAllTagsOfAnyType($tags->all());
    }
}
