<?php

namespace App\Support\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Spatie\QueryBuilder\Filters\Filter;

class RelatedFilter implements Filter
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var string
     */
    protected string $query = '';

    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $value = is_array($value) ? implode(',', $value) : $value;

        // Set model
        $this->model = $query->getModel()->findByHash($value);

        $this->setQuery();

        // Merge collection models
        $models = collect();

        foreach ($this->getCollections() as $collection) {
            $models = $models->merge($collection);
        }

        $ids = $models->pluck('id')->toArray();
        $idsOrder = implode(',', $ids);

        return $query
            ->whereIn('id', $ids)
            ->orderByRaw("FIELD(id, {$idsOrder})");
    }

    /**
     * @doc https://stackoverflow.com/a/16427088
     *
     * @return self
     */
    protected function setQuery(): self
    {
        $this->query = preg_replace('~[^\p{L}]++~u', ' ', $this->model->name);
        $this->query = preg_replace('/\s+/', ' ', trim($this->query));

        return $this;
    }

    /**
     * @return array
     */
    protected function getCollections(): array
    {
        return [
            $this->getModelsByQuery(),
            $this->getModelsWithTags(),
            $this->getModelsWithCollection(),
        ];
    }

    /**
     * @return Collection
     */
    protected function getModelsByQuery()
    {
        return $this->model
            ->search($this->query)
            ->select('id')
            ->where('id', '<>', $this->model->id)
            ->collapse('id')
            ->from(0)
            ->take(12)
            ->get();
    }

    /**
     * @return Collection
     */
    protected function getModelsWithTags()
    {
        return $this->model
            ->select('id')
            ->whereKeyNot($this->model->id)
            ->withAnyTagsOfAnyType($this->model->tags)
            ->inRandomSeedOrder()
            ->take(12)
            ->get();
    }

    /**
     * @return Collection
     */
    protected function getModelsWithCollection()
    {
        return $this->model
            ->select('id')
            ->whereKeyNot($this->model->id)
            ->withAnyCollectionsOfAnyType($this->model->collections)
            ->inRandomSeedOrder()
            ->take(12)
            ->get();
    }
}
