<?php

namespace App\Support\QueryBuilder\Filters\Media;

use App\Models\Channel;
use App\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filters\Filter;

class RelatedFilter implements Filter
{
    /**
     * @var Media
     */
    protected $media;

    /**
     * @var string
     */
    protected ?string $search = null;

    public function __invoke(Builder $query, $value, string $property): Builder
    {
        // Convert arrays to string
        $value = is_array($value) ? implode(' ', $value) : $value;

        // Set model
        $this->media = $query->getModel()->findByHash($value);

        // Set search query
        $this->setSearchQuery();

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
     * @doc https://stackoverflow.com/a/16427088
     *
     * @return self
     */
    protected function setSearchQuery(): self
    {
        $this->search = preg_replace('~[^\p{L}]++~u', ' ', $this->media->name);
        $this->search = preg_replace('/\s+/', ' ', trim($this->search));

        return $this;
    }

    /**
     * @return array
     */
    protected function getCollections(): array
    {
        return [
            $this->getModelsByChannel(),
            $this->getModelsByRandom(),
            $this->getModelsByQuery(),
            $this->getModelsByTags(),
            $this->getModelsByAdditional(),
        ];
    }

    /**
     * @return Collection
     */
    protected function getModelsByChannel()
    {
        return Media::search($this->search)
            ->select('id')
            ->where('id', '<>', $this->media->id)
            ->whereMatch('model_type', Channel::class)
            ->whereMatch('model_id', $this->media->model->id)
            ->collapse('id')
            ->from(0)
            ->take(9)
            ->get();
    }

    /**
     * @return Collection
     */
    protected function getModelsByRandom()
    {
        return Media::select('id')
            ->whereKeyNot($this->media->id)
            ->where('model_type', Channel::class)
            ->where('model_id', $this->media->model->id)
            ->inRandomSeedOrder()
            ->take(6)
            ->get();
    }

    /**
     * @return Collection
     */
    protected function getModelsByQuery()
    {
        return Media::search($this->search)
            ->select('id')
            ->where('id', '<>', $this->media->id)
            ->collapse('id')
            ->from(0)
            ->take(9)
            ->get();
    }

    /**
     * @return Collection
     */
    protected function getModelsByTags()
    {
        return Media::select('id')
            ->whereKeyNot($this->media->id)
            ->withAnyTagsOfAnyType(
                $this->media->tags
            )
            ->inRandomSeedOrder()
            ->take(9)
            ->get();
    }

    /**
     * @return Collection
     */
    public function getModelsByAdditional()
    {
        return Media::select('id')
            ->whereKeyNot($this->media->id)
            ->inRandomSeedOrder()
            ->take(9)
            ->get();
    }
}
