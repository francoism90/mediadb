<?php

namespace App\Support\QueryBuilder\Filters\Video;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class TypeFilter implements Filter
{
    public const TYPES = [
        ['key' => 'favorites', 'scope' => 'withFavorites'],
        ['key' => 'followings', 'scope' => 'withFollowings'],
    ];

    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $value = is_array($value) ? implode(' ', $value) : $value;

        $types = collect(self::TYPES);

        $type = $types->firstWhere('key', $value) ?? [];

        return $query->scopes($type['scope']);
    }
}
