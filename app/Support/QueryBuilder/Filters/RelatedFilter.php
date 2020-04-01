<?php

namespace App\Support\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filters\Filter;

class RelatedFilter implements Filter
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var Model
     */
    protected $item;

    /**
     * @var string
     */
    protected ?string $searchQuery = null;

    public function __invoke(Builder $query, $value, string $property): Builder
    {
        // Convert arrays to string
        $value = is_array($value) ? implode(' ', $value) : $value;

        // Set models and search query
        $this->setSearchModels($query->getModel(), $value)
             ->setSearchQuery();

        // Merge all models
        $models = collect();

        foreach ($this->getCollections() as $collection) {
            $models = $models->merge($collection);
        }

        // Return ordered models
        $ids = $models->pluck('id')->toArray();
        $idsOrder = implode(',', $ids);

        // Remove any current OrderBy
        $query->getQuery()->orders = null;

        return $query->whereIn('id', $ids)
                     ->orderByRaw(DB::raw("FIELD(id, $idsOrder)"));
    }

    /**
     * @param Model  $model
     * @param string $value
     *
     * @return self
     */
    private function setSearchModels(Model $model, string $value): self
    {
        $this->model = $model;

        $this->item = $model->getModelByKey($value);

        return $this;
    }

    /**
     * @param string $str
     *
     * @return self
     */
    private function setSearchQuery(): self
    {
        // https://stackoverflow.com/a/16427088
        $this->searchQuery = preg_replace('~[^\p{L}]++~u', ' ', $this->item->name);

        // Remove any whitespace
        $this->searchQuery = preg_replace('/\s+/', ' ', trim($this->searchQuery));

        return $this;
    }

    /**
     * @return array
     */
    private function getCollections(): array
    {
        return [
            $this->getRelatedModels(),
            $this->getRelatedTags(),
            $this->getRelatedRandom(),
        ];
    }

    /**
     * @return Collection
     */
    private function getRelatedModels()
    {
        return $this->model
            ->search($this->searchQuery)
            ->select('id')
            ->where('id', '<>', $this->item->id)
            ->collapse('id')
            ->from(0)
            ->take(16)
            ->get();
    }

    /**
     * @return Collection
     */
    private function getRelatedTags()
    {
        return $this->model
            ->select('id')
            ->whereKeyNot($this->item->id)
            ->withAnyTagsOfAnyType(
                $this->item->tags
            )
            ->inRandomOrder(
                $this->model->getRandomSeed()
            )
            ->take(16)
            ->get();
    }

    /**
     * @return Collection
     */
    public function getRelatedRandom()
    {
        return $this->model
            ->select('id')
            ->whereKeyNot($this->item->id)
            ->inRandomOrder(
                $this->model->getRandomSeed()
            )
            ->take(8)
            ->get();
    }
}
