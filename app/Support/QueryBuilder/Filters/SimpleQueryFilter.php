<?php

namespace App\Support\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\Filters\Filter;

class SimpleQueryFilter implements Filter
{
    /**
     * @var string
     */
    protected ?string $queryStr = null;

    /**
     * @param Builder      $query
     * @param string|array $value
     * @param string       $property
     *
     * @return Builder
     */
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        // Convert arrays to string
        $value = is_array($value) ? implode(' ', $value) : $value;

        $this->setQueryString((string) $value);

        // Get all models
        $models = $this->getQueryModels($query->getModel());

        // Return results
        $ids = $models->pluck('id')->toArray();
        $idsOrder = implode(',', $ids);

        return $query->whereIn('id', $ids)
                     ->orderByRaw("FIELD(id, {$idsOrder})");
    }

    /**
     * @param string $str
     *
     * @return void
     */
    protected function setQueryString(string $str = ''): void
    {
        // Replace special chars
        $this->queryStr = str_replace(['.', '_'], ' ', $str);

        // Keep ASCII > 127
        $this->queryStr = filter_var(
            $this->queryStr,
            FILTER_SANITIZE_STRING,
            FILTER_FLAG_NO_ENCODE_QUOTES |
            FILTER_FLAG_STRIP_LOW
        );

        // Remove whitespace
        $this->trimQueryString();
    }

    /**
     * @return void
     */
    protected function trimQueryString(): void
    {
        $this->queryStr = preg_replace('/\s+/', ' ', trim($this->queryStr));
    }

    /**
     * @return Collection
     */
    protected function getQueryModels(Model $model)
    {
        return $model->search($this->queryStr)
            ->select(['name'])
            ->collapse('id')
            ->from(0)
            ->take(1500)
            ->get();
    }
}
