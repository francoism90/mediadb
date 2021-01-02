<?php

namespace App\Support\QueryBuilder\Filters\Collection;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class TypeFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $types = is_string($value) ? explode(',', $value) : $value;

        $types = collect($types);

        return $query
            ->when($types->contains('subscribed'), fn ($query) => $query->whereHas('subscribers', function (Builder $query): void {
                $query->where('id', auth()->user()->id);
            }));
    }
}
