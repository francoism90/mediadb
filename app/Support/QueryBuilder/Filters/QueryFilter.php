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

        $models = $this->getModels($query, $value);

        return $query
            ->when($models->isEmpty(), function ($query) use ($table) {
                return $query->whereNull(sprintf('%s.id', $table));
            }, function ($query) use ($models, $table) {
                $ids = $models->pluck('id');
                $idsOrder = $models->implode('id', ',');

                return $query
                    ->whereIn(sprintf('%s.id', $table), $ids)
                    ->orderByRaw(sprintf('FIELD(%s.id, %s)', $table, $idsOrder));
            });
    }

    protected function getModels(Builder $query, string $value = ''): Collection
    {
        $value = $this->sanitize($value);

        $models = collect();

        // e.g. this book 1, this book, this
        for ($i = $value->take(self::QUERY_WORD_LIMIT)->count(); $i >= 1; --$i) {
            $terms = $value->take($i)->implode(' ');

            $models = $models->merge(
                $query
                    ->getModel()
                    ->search($terms)
                    ->take(self::QUERY_RESULT_LIMIT)
                    ->get()
            );
        }

        return $models;
    }

    protected function sanitize(string $value = ''): Collection
    {
        $value = Str::ascii($value);

        return Str::of($value)->matchAll(self::FILTER_WORD_REGEX);
    }
}
