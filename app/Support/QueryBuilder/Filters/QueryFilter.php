<?php

namespace App\Support\QueryBuilder\Filters;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
    protected string $query = '';

    /**
     * @var array
     */
    protected array $tags = [];

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

        // Set model
        $this->model = $query->getModel();

        // Set query
        $this->setQuery($value)
             ->extractTags();

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
     * @param string $str
     *
     * @return self
     */
    protected function setQuery(string $str = ''): self
    {
        // Keep ASCII > 127
        $this->query = filter_var(
            $str,
            FILTER_SANITIZE_STRING,
            FILTER_FLAG_STRIP_LOW |
            FILTER_FLAG_STRIP_BACKTICK |
            FILTER_FLAG_NO_ENCODE_QUOTES
        );

        // Replace specials chars
        $this->replaceInQuery(['.', ',', '_', '*']);

        return $this;
    }

    /**
     * @doc https://stackoverflow.com/a/35498078
     *
     * @return self
     */
    protected function extractTags(): self
    {
        preg_match_all(
            '/tag:([\p{Pc}\p{Pd}\p{N}\p{L}\p{Mn}]+)/u',
            $this->query,
            $matches
        );

        // Remove tags from query
        $this->replaceInQuery($matches[0] ?? []);

        // Keep unique tag slugs
        $this->tags = array_unique($matches[1] ?? []);

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
        ];
    }

    /**
     * @param array|string $find
     * @param array|string $replace
     *
     * @return self
     */
    protected function replaceInQuery($find = [], $replace = ' '): self
    {
        $this->query = str_replace($find, $replace, $this->query);
        $this->query = preg_replace('/\s+/', ' ', trim($this->query));

        return $this;
    }

    /**
     * @return \Illuminate\Support\Collection
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
     * @return \Illuminate\Support\Collection
     */
    protected function getModelsWithTags()
    {
        if (!$this->model->tags || !$this->tags) {
            return collect();
        }

        $tags = Tag::withSlugTranslated($this->tags)->get();

        return $this->model
            ->select('id')
            ->withAllTagsOfAnyType($tags)
            ->inRandomSeedOrder()
            ->get();
    }
}
