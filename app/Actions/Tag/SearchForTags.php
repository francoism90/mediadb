<?php

namespace App\Actions\Tag;

use App\Models\Tag;
use Illuminate\Support\Arr;
use Laravel\Scout\Builder;
use MeiliSearch\Endpoints\Indexes;

class SearchForTags
{
    public function __invoke(array $data): Builder
    {
        $params = $this->getSearchOptions($data);

        return Tag::search(
            $params['query'] ?? '*',
            function (Indexes $meilisearch, string $query, array $options) use ($params) {
                $options = array_merge($options, Arr::only($params, ['limit', 'sort']));

                return $meilisearch->search($query, $options);
            })
        ->when($params['id'], fn ($engine, $ids) => $engine->whereIn('id', $ids))
        ->when($params['type'], fn ($engine, $tags) => $engine->whereIn('type', $tags));
    }

    protected function getSearchOptions(array $data): array
    {
        $option = fn (string $key, mixed $default = null) => data_get($data, $key, $default);

        return [
            'query' => $option('filter.query'),
            'id' => $option('filter.id'),
            'type' => $option('filter.type'),
            'sort' => (array) $option('sort'),
            'limit' => (int) $option('size', 24),
        ];
    }
}
