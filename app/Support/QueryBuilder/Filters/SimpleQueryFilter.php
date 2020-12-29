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
    public const TAG_REGEX = '/tag:([\p{Pc}\p{Pd}\p{N}\p{L}\p{Mn}]+)/u';

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

        // Results
        $queryModels = $this->getModelsByQuery($query, $value);
        $tagModels = $this->getTagModels($query, $value);

        return $query
            ->when($queryModels->isNotEmpty(), function ($query) use ($queryModels) {
                $ids = $queryModels->pluck('id');
                $idsOrder = $queryModels->implode('id', ',');

                return $query
                    ->whereIn('id', $ids)
                    ->orderByRaw("FIELD(id, {$idsOrder})");
            })
            ->when($tagModels->isNotEmpty(), function ($query) use ($tagModels) {
                return $query->withAnyTagsOfAnyType($tagModels);
            })
            ->orderBy('id');
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

        // Replace dots and underscores
        $value = str_replace(['.', ',', '_'], ' ', $value);

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
        // Replace tag queries (e.g. tag:foo)
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
    protected function getTagModels(Builder $query, string $value): Collection
    {
        if (!$query->getModel()->tags) {
            return collect();
        }

        $matches = Str::of($value)->matchAll(self::TAG_REGEX)->toArray();

        return Tag::withSlugTranslated($matches)->get();
    }
}
