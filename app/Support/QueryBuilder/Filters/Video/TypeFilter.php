<?php

namespace App\Support\QueryBuilder\Filters\Video;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class TypeFilter implements Filter
{
    public const SCOPES = [
        ['key' => 'favorites', 'value' => 'withFavorites'],
        ['key' => 'followings', 'value' => 'withFollowings'],
    ];

    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $value = is_string($value) ? explode(',', $value) : $value;

        $scopes = collect(self::SCOPES)->whereIn('key', $value);

        return $query->scopes($scopes->implode('value'));
    }
}
