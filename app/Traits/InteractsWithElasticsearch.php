<?php

namespace App\Traits;

use Laravel\Scout\Builder;
use ONGR\ElasticsearchDSL\Query\FullText\MultiMatchQuery;
use ONGR\ElasticsearchDSL\Query\FullText\SimpleQueryStringQuery;
use ONGR\ElasticsearchDSL\Search;

trait InteractsWithElasticsearch
{
    /**
     * @param string $value
     * @param int    $size
     *
     * @return Builder
     */
    public function queryString(string $value = '', int $size = 10): Builder
    {
        return $this->search($value, function ($client, $body) use ($value, $size) {
            $simpleQueryStringQuery = new SimpleQueryStringQuery(
                $value,
                [
                    'fields' => config('elasticsearch.query_fields', []),
                    'parameters' => config('elasticsearch.query_parameters', []),
                ],
            );

            $body = new Search();
            $body->setSize($size);
            $body->addQuery($simpleQueryStringQuery);

            return $client->search([
                'index' => $this->searchableAs(),
                'body' => $body->toArray(),
            ]);
        });
    }

    /*
     * @param string  $value
     * @param int     $size
     *
     * @return Builder
     */
    public function multiMatchQuery(string $value = '', int $size = 10): Builder
    {
        return $this->search($value, function ($client, $body) use ($value, $size) {
            $multiMatchQuery = new MultiMatchQuery(
                config('elasticsearch.query_fields', []),
                $value,
                config('elasticsearch.query_parameters', []),
            );

            $body = new Search();
            $body->setSize($size);
            $body->addQuery($multiMatchQuery);

            return $client->search([
                'index' => $this->searchableAs(),
                'body' => $body->toArray(),
            ]);
        });
    }
}
