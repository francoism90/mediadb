<?php

namespace App\Support\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\Filters\Filter;

class QueryFilter implements Filter
{
    public const FILTER_WORD_REGEX = '/[\p{L}\p{N}]+/u';
    public const QUERY_WORD_LIMIT = 8;
    public const QUERY_RESULT_LIMIT = 10000;

    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $value = is_array($value) ? implode(' ', $value) : $value;

        $table = $query->getModel()->getTable();

        $models = $this->getModelsByQuery($query, $value);

        return $query
            ->when($models->isEmpty(), function ($query) use ($table) {
                return $query->whereNull("{$table}.id");
            }, function ($query) use ($models, $table) {
                $ids = $models->pluck('id');
                $idsOrder = $models->implode('id', ',');

                return $query
                    ->whereIn("{$table}.id", $ids)
                    ->orderByRaw("FIELD({$table}.id, {$idsOrder})");
            });
    }

    protected function getModelsByQuery(Builder $query, string $value = ''): Collection
    {
        $models = collect();

        $value = $this->sanitizeQuery($value);

        // Perform partial searching (e.g. this book 1, this book, this)
        for ($i = $value->take(self::QUERY_WORD_LIMIT)->count(); $i >= 1; --$i) {
            $queryValue = $value->take($i)->implode(' ');

            $models = $models->merge(
                $query
                    ->getModel()
                    ->search($queryValue)
                    ->take(self::QUERY_RESULT_LIMIT)
                    ->get()
            );
        }

        return $models;
    }

    protected function sanitizeQuery(string $value = ''): Collection
    {
        $value = Str::ascii($value);

        return Str::of($value)->matchAll(self::FILTER_WORD_REGEX);
    }
}
