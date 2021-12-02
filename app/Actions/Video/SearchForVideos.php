<?php

namespace App\Actions\Video;

use App\Models\Video;
use Illuminate\Support\Arr;
use Laravel\Scout\Builder;
use MeiliSearch\Endpoints\Indexes;

class SearchForVideos
{
    public function __invoke(array $data): Builder
    {
        $params = $this->getSearchOptions($data);

        return Video::search(
            $params['query'] ?? '*',
            function (Indexes $meilisearch, string $query, array $options) use ($params) {
                $options = array_merge($options, Arr::only($params, ['limit', 'sort']));

                return $meilisearch->search($query, $options);
            })
        ->when($params['id'], fn ($builder, $ids) => $builder->whereIn('id', $ids))
        ->when($params['tags'], fn ($builder, $tags) => $builder->where('tags', $tags));
    }

    protected function getSearchOptions(array $data): array
    {
        $option = fn (string $key, mixed $default = null) => data_get($data, $key, $default);

        // We need to use an additional query
        $ids = array_merge($option('id'), $this->getVideoIdsByType($option('type')));

        return [
            'id' => $ids,
            'query' => $option('query'),
            'tags' => $option('tags'),
            'sort' => (array) $option('sort'),
            'limit' => (int) $option('size', 24),
        ];
    }

    protected function getVideoIdsByType(mixed $types = null): ?array
    {
        if (!$types) {
            return null;
        }

        return Video::active()
            ->withAnyType($types, auth()?->user())
            ->pluck('id')
            ->take(500)
            ->all();
    }
}
