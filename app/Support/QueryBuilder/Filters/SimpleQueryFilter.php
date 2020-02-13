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
     * @var Model
     */
    protected $model;

    /**
     * @var string
     */
    protected ?string $searchQuery = null;

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

        $this->setSearchQuery((string) $value);

        if (!$this->searchQuery || '*' === $this->searchQuery) {
            return $query->where('id', 0);
        }

        // Get requested model
        $this->model = $query->getModel();

        // Get all models
        $models = $this->getQueryModels();

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
    private function setSearchQuery(string $str = ''): void
    {
        // Keep ASCII > 127
        $this->searchQuery = filter_var(
            $str,
            FILTER_SANITIZE_STRING,
            FILTER_FLAG_NO_ENCODE_QUOTES |
            FILTER_FLAG_STRIP_LOW
        );

        // Remove whitespace
        $this->searchQuery = preg_replace('/\s+/', ' ', trim($this->searchQuery));
    }

    /**
     * @return Collection
     */
    private function getQueryModels(): Collection
    {
        return $this->model->search($this->searchQuery)
            ->select(['name', 'description'])
            ->collapse('id')
            ->from(0)
            ->take(10000)
            ->get();
    }
}
