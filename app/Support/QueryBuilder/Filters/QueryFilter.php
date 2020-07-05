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
     * @var string
     */
    protected ?string $queryStr = null;

    /**
     * @var array
     */
    protected ?array $queryTags = [];

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

        $this->setQuery((string) $value)
             ->setQueryTags();

        // Merge all models
        $models = $this->getModelsByQuery($query->getModel());

        $models = $models->merge(
            $this->getModelsByTags($query->getModel())
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
     * @return self
     */
    protected function setQuery(string $str = ''): self
    {
        // Keep ASCII > 127
        $this->queryStr = filter_var(
            $str,
            FILTER_SANITIZE_STRING,
            FILTER_FLAG_NO_ENCODE_QUOTES |
            FILTER_FLAG_STRIP_LOW
        );

        // Remove some specials chars
        $this->replaceInQueryString(['.', ',', '_', '*']);

        return $this;
    }

    /**
     * @return self
     */
    protected function setQueryTags(): self
    {
        // https://stackoverflow.com/a/35498078
        preg_match_all('/#([\p{Pc}\p{Pd}\p{N}\p{L}\p{Mn}]+)/u', $this->queryStr, $matches);

        // Remove tags from query (if any)
        $this->replaceInQueryString($matches[0] ?? []);

        // Get all tag slugs
        $this->queryTags = array_unique($matches[1]) ?? [];

        return $this;
    }

    /**
     * @param array $matches
     *
     * @return self
     */
    protected function replaceInQueryString(array $replace = []): self
    {
        $this->queryStr = str_replace($replace, ' ', $this->queryStr);

        $this->trimQueryString();

        return $this;
    }

    /**
     * @return self
     */
    protected function trimQueryString(): self
    {
        // Remove double spaces and tabs
        $this->queryStr = preg_replace('/\s+/', ' ', trim($this->queryStr));

        return $this;
    }

    /**
     * @return Collection
     */
    protected function getModelsByQuery(Model $model)
    {
        return $model->search($this->queryStr)
            ->select(['id'])
            ->from(0)
            ->take(10000)
            ->get();
    }

    /**
     * @return Collection
     */
    protected function getModelsByTags(Model $model)
    {
        if (!$this->queryTags) {
            return collect();
        }

        $tags = Tag::withSlugTranslated($this->queryTags)->get();

        return $model->withAllTagsOfAnyType($tags)
                     ->inRandomSeedOrder()
                     ->get();
    }
}
