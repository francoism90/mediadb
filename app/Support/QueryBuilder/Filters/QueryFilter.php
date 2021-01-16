<?php

namespace App\Support\QueryBuilder\Filters;

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
        $value = is_array($value) ? implode(' ', $value) : $value;

        $value = $this->sanitize($value);

        $models = $this->getQueryModels($query, $value);

        return $query
            ->when($models->isNotEmpty(), function ($query) use ($models) {
                $ids = $models->pluck('id');
                $idsOrder = $models->implode('id', ',');

                return $query
                    ->whereIn('id', $ids)
                    ->orderByRaw("FIELD(id, {$idsOrder})");
            }, function ($query) {
                return $query->whereNull('id'); // force empty result
            });
    }

    /**
     * @param Builder $query
     * @param string  $value
     *
     * @return Collection
     */
    protected function getQueryModels(Builder $query, string $value = ''): Collection
    {
        return $query
            ->getModel()
            ->multiMatchQuery($value, 10000)
            ->get();
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function sanitize(string $value = ''): ?string
    {
        $value = filter_var(
            $value,
            FILTER_SANITIZE_STRING,
            FILTER_FLAG_STRIP_LOW |
            FILTER_FLAG_STRIP_BACKTICK |
            FILTER_FLAG_NO_ENCODE_QUOTES
        );

        // Replace symbols
        $value = str_replace(['.', ',', '_'], ' ', $value);

        // Replace whitespace with a single space
        $value = preg_replace('/\s+/', ' ', $value);

        return $value;
    }
}
