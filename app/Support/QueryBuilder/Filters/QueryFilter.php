<?php

namespace App\Support\QueryBuilder\Filters;

use App\Services\SearchService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\Filters\Filter;

class QueryFilter implements Filter
{
    /**
     * @param Builder      $query
     * @param string|array $value
     * @param string       $property
     *
     * @return Builder
     */
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        // Sanitize query
        $value = is_array($value) ? implode(' ', $value) : $value;

        // Get correct table
        $table = $query->getModel()->getTable();

        // Get matching models
        $models = $this->getModelsByQuery($query, $value);

        return $query
            ->when($models->isEmpty(), function ($query) {
                return $query->whereNull('id');
            }, function ($query) use ($models, $table) {
                $ids = $models->pluck('id');
                $idsOrder = $models->implode('id', ',');

                return $query
                    ->whereIn("{$table}.id", $ids)
                    ->orderByRaw("FIELD({$table}.id, {$idsOrder})");
            });
    }

    /**
     * @param Builder $query
     * @param string  $value
     *
     * @return Collection
     */
    protected function getModelsByQuery(Builder $query, string $value = ''): Collection
    {
        $searchService = new SearchService();
        $searchService->search($query, $value);

        return $searchService->getResults();
    }
}
