<?php

namespace App\Support\QueryBuilder\Filters\Video;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class TypeFilter implements Filter
{
    public const ALLOWED_SCOPES = [
        ['value' => 'favorites', 'scope' => 'withFavorites'],
        ['value' => 'following', 'scope' => 'withFollowing'],
        ['value' => 'viewed', 'scope' => 'withViewed'],
    ];

    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $value = is_string($value) ? explode(',', $value) : $value;

        $scopes = collect(static::ALLOWED_SCOPES)->whereIn('value', $value);

        return $query->scopes(
            $scopes->implode('scope', ',')
        );
    }
}
