<?php

namespace App\Support\QueryBuilder\Filters;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ONGR\ElasticsearchDSL\Query\FullText\SimpleQueryStringQuery;
use ONGR\ElasticsearchDSL\Search;
use Spatie\QueryBuilder\Filters\Filter;

class SimpleQueryFilter implements Filter
{
    protected const TAG_REGEX = '/tag:([\p{Pc}\p{Pd}\p{N}\p{L}\p{Mn}]+)/u';

    /**
     * @param Builder      $query
     * @param string|array $value
     * @param string       $property
     *
     * @return Builder
     */
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        // Sanitize value
        $value = is_array($value) ? implode(' ', $value) : $value;

        $value = $this->sanitize($value);

        // Merge collection models
        $models = collect();

        $models = $models->merge($this->getModelsByQuery($query, $value));
        $models = $models->merge($this->getModelsWithTags($query, $value));

        // Return query
        $ids = $models->pluck('id')->toArray();
        $idsOrder = implode(',', $ids);

        return $query
            ->whereIn('id', $ids)
            ->orderByRaw("FIELD(id, {$idsOrder})");
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function sanitize(string $value): string
    {
        $value = filter_var(
            $value,
            FILTER_SANITIZE_STRING,
            FILTER_FLAG_STRIP_LOW |
            FILTER_FLAG_STRIP_BACKTICK |
            FILTER_FLAG_NO_ENCODE_QUOTES
        );

        // Replace whitespace with a single space
        $value = preg_replace('/\s+/', ' ', $value);

        return $value;
    }

    /**
     * @param Builder $query
     * @param string  $value
     *
     * @return Collection
     */
    protected function getModelsByQuery(Builder $query, string $value): Collection
    {
        // Replace tags (if any)
        $value = (string) Str::of($value)->replaceMatches(self::TAG_REGEX, '')->trim();

        return $query
            ->getModel()
            ->search($value, function ($client, $body) use ($query, $value) {
                $simpleQueryStringQuery = new SimpleQueryStringQuery(
                    $value, ['fields' => ['name^5', 'description', 'overview']]
                );

                $body = new Search();
                $body->addQuery($simpleQueryStringQuery);

                return $client->search([
                    'index' => $query->getModel()->searchableAs(),
                    'body' => $body->toArray(),
                ]);
            })
            ->take(10000)
            ->get();
    }

    /**
     * @param Builder $query
     * @param string  $value
     *
     * @return Collection
     */
    protected function getModelsWithTags(Builder $query, string $value): Collection
    {
        if (!$query->getModel()->tags) {
            return collect();
        }

        $matches = Str::of($value)->matchAll(self::TAG_REGEX)->toArray();

        $tags = Tag::withSlugTranslated($matches)->get();

        return $query
            ->getModel()
            ->withAllTagsOfAnyType($tags)
            ->inRandomSeedOrder()
            ->take(10000)
            ->get();
    }
}
