<?php

namespace App\Actions\Video;

use App\Models\Video;
use Laravel\Scout\Builder;
use MeiliSearch\Endpoints\Indexes;
use Illuminate\Support\Arr;

class SearchForVideos
{
    public function __invoke(array $data): Builder
    {
        $params = $this->getSearchOptions($data);
        logger($params);

        return Video::search(
            $params['query'],
            function (Indexes $meilisearch, string $query, array $options) use ($params) {
                $options = array_merge($options, Arr::only($params, ['limit', 'sort']));

                return $meilisearch->search($query, $options);
        })
        ->when($params['id'], fn ($engine, $ids) => $engine->whereIn('id', $ids))
        ->when($params['tags'], fn ($engine, $tags) => $engine->where('tags', $tags));
    }

    protected function getSearchOptions(array $data): array
    {
        $option = fn (string $key, mixed $default = null) => data_get($data, $key, $default);

        return [
            'query' => $option('filter.query', '*'),
            'id' => $option('filter.id', null),
            'tags' => $option('filter.tags', null),
            'sort' => (array) $option('sort', null),
            'page' => $option('page', 1),
            'limit' => $option('size', 24),
        ];
    }
}
