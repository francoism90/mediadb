<?php

namespace App\Support\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ONGR\ElasticsearchDSL\Query\FullText\MultiMatchQuery;
use ONGR\ElasticsearchDSL\Search;
use Spatie\QueryBuilder\Filters\Filter;

class RelatedFilter implements Filter
{
    protected const NAME_REGEX = '/[^A-Za-z0-9\-]/u';

    /**
     * @param Builder      $query
     * @param string|array $value
     * @param string       $property
     *
     * @return Builder
     */
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $value = is_string($value) ? explode(',', $value) : $value;

        $models = $query->getModel()->convertHashidsToModels($value);

        // Find query matches
        $results = collect();

        foreach ($models as $model) {
            $value = (string) Str::of($model->name)
                ->replaceMatches(self::NAME_REGEX, ' ')
                ->trim();

            $results = $results->merge(
                $this->getModelsByQuery($query, $value)
            );
        }

        return $query
            ->where(function ($query) use ($results, $models) {
                $query
                    ->whereIn('id', $results->pluck('id'))
                    ->whereNotIn('id', $models->pluck('id'));
            })
            ->orWhere(function ($query) use ($models) {
                $query
                    ->withAllCollectionsOfAnyType(
                        $models->pluck('collections')->collapse()
                    )
                    ->whereNotIn('id', $models->pluck('id'))
                    ->inRandomSeedOrder()
                    ->take(12);
            })
            ->orWhere(function ($query) use ($models) {
                $query
                    ->withAnyTagsOfAnyType(
                        $models->pluck('tags')->collapse()
                    )
                    ->whereNotIn('id', $models->pluck('id'))
                    ->inRandomSeedOrder()
                    ->take(12);
            })
            ->when($results->isNotEmpty(), function ($query) use ($results) {
                $idsOrder = $results->implode('id', ',');

                return $query->orderByRaw("FIELD(id, {$idsOrder}) DESC");
            })
            ->orderBy('id');
    }

    /**
     * @param Builder $query
     * @param sting   $value
     *
     * @return Collection
     */
    protected function getModelsByQuery(Builder $query, string $value): Collection
    {
        return $query
            ->getModel()
            ->search($value, function ($client, $body) use ($query, $value) {
                $multiMatchQuery = new MultiMatchQuery(
                    ['name^5', 'description', 'overview'], $value
                );

                $body = new Search();
                $body->setSize(12);
                $body->addQuery($multiMatchQuery);

                return $client->search([
                    'index' => $query->getModel()->searchableAs(),
                    'body' => $body->toArray(),
                ]);
            })
            ->get();
    }
}
