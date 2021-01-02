<?php

namespace App\Support\QueryBuilder\Filters\Video;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class TypeFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $types = is_string($value) ? explode(',', $value) : $value;

        $types = collect($types);

        return $query
            ->when($types->contains('favorited'), fn ($query) => $query->whereHas('favoriters', function (Builder $query) {
                $query->where('id', auth()->user()->id);
            }))
            ->when($types->contains('liked'), fn ($query) => $query->whereHas('likers', function (Builder $query) {
                $query->where('id', auth()->user()->id);
            }));
    }
}
