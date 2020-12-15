<?php

namespace App\Support\QueryBuilder\Sorts\Video;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use ONGR\ElasticsearchDSL\Query\TermLevel\IdsQuery;
use ONGR\ElasticsearchDSL\Sort\FieldSort;
use Spatie\QueryBuilder\Sorts\Sort;

class DurationSorter implements Sort
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param bool                                  $descending
     * @param string                                $property
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function __invoke(Builder $query, bool $descending, string $property): Builder
    {
        $query->getQuery()->orders = null;

        $direction = $descending ? 'DESC' : 'ASC';

        $models = $this->getModels($query, $direction);

        $ids = $models->pluck('id')->toArray();
        $idsOrder = implode(',', $ids);

        return $query
            ->whereIn('id', $ids)
            ->orderByRaw("FIELD(id, {$idsOrder})");
    }

    /**
     * @param Builder $query
     *
     * @return Collection
     */
    protected function getModels(Builder $query, string $direction): Collection
    {
        return $query
            ->getModel()
            ->search('*', function ($client, $body) use ($query, $direction) {
                $idsQuery = new IdsQuery(
                    $query->pluck('id')->toArray()
                );

                $durationSorter = new FieldSort('duration', $direction);

                $body->addQuery($idsQuery);
                $body->addSort($durationSorter);

                return $client->search([
                    'index' => $query->getModel()->searchableAs(),
                    'body' => $body->toArray(),
                ]);
            })
            ->get();
    }
}
