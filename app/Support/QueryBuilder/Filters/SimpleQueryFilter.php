<?php

namespace App\Support\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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
        $value = is_array($value) ? implode(' ', $value) : $value;

        $this->setQueryString((string) $value);

        // Invalid queries
        if (!$this->queryStr || '*' === $this->queryStr) {
            return $query->where('id', 0);
        }

        // Get all models
        $models = $this->getQueryModels($query->getModel());

        // Return results
        $ids = $models->pluck('id')->toArray();
        $idsOrder = implode(',', $ids);

        return $query->whereIn('id', $ids)
                     ->orderByRaw(DB::raw("FIELD(id, $idsOrder)"));
    }

    /**
     * @param string $str
     *
     * @return void
     */
    private function setQueryString(string $str = ''): void
    {
        // Keep ASCII > 127
        $this->queryStr = filter_var(
            $str,
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
    private function trimQueryString(): void
    {
        $this->queryStr = preg_replace('/\s+/', ' ', trim($this->queryStr));
    }

    /**
     * @return Collection
     */
    private function getQueryModels(Model $model)
    {
        return $model->search($this->queryStr)
            ->select(['name', 'description'])
            ->collapse('id')
            ->from(0)
            ->take(10000)
            ->get();
    }
}
