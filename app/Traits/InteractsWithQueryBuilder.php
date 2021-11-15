<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait InteractsWithQueryBuilder
{
    public static function getQueryModel(Builder $query): Model
    {
        return $query->getModel();
    }

    public static function getQueryTable(Builder $query): string
    {
        return $query->getModel()->getTable();
    }

    public static function getQueryIds(Builder $query): Collection
    {
        return $query->pluck('id');
    }

    public static function getQueryCacheKey(Builder $query, string $name): string
    {
        return sprintf('query_%s_%s', static::getQueryTable($query), Str::kebab($name));
    }
}
