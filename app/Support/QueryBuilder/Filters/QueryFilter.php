<?php

namespace App\Support\QueryBuilder\Filters;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filters\Filter;

class QueryFilter implements Filter
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
     * @var string
     */
    protected ?array $searchTags = [];

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

        // Set all tags
        $this->setAllTags();

        // Merge all models
        $models = collect();

        foreach ($this->getSearchCollections() as $collection) {
            $models = $models->merge($collection);
        }

        $ids = $models->pluck('id')->toArray();
        $idsOrder = implode(',', $ids);

        return $query->whereIn('id', $ids)
                     ->orderByRaw(DB::raw("FIELD(id, $idsOrder)"));
    }

    /**
     * @return array
     */
    private function getSearchCollections(): array
    {
        return [
            $this->getQueryModels(),
            $this->getTagsModels(),
        ];
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
        $this->trimSearchQuery();
    }

    /**
     * @return void
     */
    private function trimSearchQuery(): void
    {
        $this->searchQuery = preg_replace('/\s+/', ' ', trim($this->searchQuery));
    }

    /**
     * @param array $matches
     *
     * @return void
     */
    private function replaceQueryInput(array $matches = []): void
    {
        $this->searchQuery = str_replace($matches, ' ', $this->searchQuery);

        $this->trimSearchQuery();
    }

    /**
     * @return self
     */
    private function setAllTags(): self
    {
        // https://stackoverflow.com/a/35498078
        preg_match_all('/#([\p{Pc}\p{Pd}\p{N}\p{L}\p{Mn}]+)/u', $this->searchQuery, $matches);

        $this->replaceQueryInput($matches[0] ?? []);
        $this->searchTags = array_unique($matches[1]) ?? [];

        return $this;
    }

    /**
     * @return Collection
     */
    private function getQueryModels()
    {
        return $this->model->search($this->searchQuery)
            ->select(['name', 'description'])
            ->collapse('id')
            ->from(0)
            ->take(10000)
            ->get();
    }

    /**
     * @return Collection
     */
    private function getTagsModels()
    {
        if (!$this->searchTags) {
            return collect();
        }

        $tags = Tag::withSlugTranslated(
            $this->searchTags
        )->get();

        return $this->model->withAllTagsOfAnyType($tags)->get();
    }
}
