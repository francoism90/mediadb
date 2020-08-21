<?php

namespace App\Support\QueryBuilder\Filters;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\Filters\Filter;

class QueryFilter implements Filter
{
    /**
     * @var Model
     */
    protected $model = null;

    /**
     * @var string
     */
    protected ?string $query = null;

    /**
     * @var array
     */
    protected ?array $tags = [];

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

        // Set model instance
        $this->model = $query->getModel();

        // Set query
        $this->setQuery((string) $value);

        // Merge all models
        $models = $this->getModelsByQuery();

        $models = $models->merge(
            $this->getModelsByTags()
        );

        // Get models
        $ids = $models->pluck('id')->toArray() ?? [];
        $idsOrder = implode(',', $ids);

        return $query->whereIn('id', $ids)
                     ->orderByRaw("FIELD(id, {$idsOrder})");
    }

    /**
     * @param string $str
     *
     * @return void
     */
    protected function setQuery(string $str = ''): void
    {
        // Keep ASCII > 127
        $this->query = filter_var(
            $str,
            FILTER_SANITIZE_STRING,
            FILTER_FLAG_NO_ENCODE_QUOTES |
            FILTER_FLAG_STRIP_LOW
        );

        // Remove specials chars
        $this->replaceQuery(['.', ',', '_', '*']);

        $this->extractTags();
    }

    /**
     * @return void
     */
    protected function extractTags(): void
    {
        // https://stackoverflow.com/a/35498078
        preg_match_all(
            '/tag:([\p{Pc}\p{Pd}\p{N}\p{L}\p{Mn}]+)/u',
            $this->query,
            $matches
        );

        // Remove tags from query
        $this->replaceQuery($matches[0] ?? []);

        // Get all tag slugs
        $this->tags = array_unique($matches[1]) ?? [];
    }

    /**
     * @param array|string $find
     * @param array|string $replace
     *
     * @return self
     */
    protected function replaceQuery($find = [], $replace = ' '): void
    {
        $this->query = str_replace($find, $replace, $this->query);
        $this->query = preg_replace('/\s+/', ' ', trim($this->query));
    }

    /**
     * @return Collection
     */
    protected function getModelsByQuery()
    {
        return $this->model
            ->search($this->query)
            ->select('id')
            ->from(0)
            ->take(10000)
            ->get();
    }

    /**
     * @return Collection
     */
    protected function getModelsByTags()
    {
        if (!$this->model->tags || !$this->tags) {
            return collect();
        }

        $tags = Tag::withSlugTranslated($this->tags)->get();

        return $this->model
            ->withAllTagsOfAnyType($tags)
            ->inRandomSeedOrder()
            ->get();
    }
}
