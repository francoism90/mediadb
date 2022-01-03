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
            $params['query'],
            function (Indexes $meilisearch, string $query, array $options) use ($params) {
                $options = array_merge($options, Arr::only($params, ['limit', 'sort']));

                return $meilisearch->search($query, $options);
            })
        ->when($params['id'], fn ($builder, $ids) => $builder->whereIn('id', $ids))
        ->when($params['tags'], fn ($builder, $tags) => $builder->whereIn('tags', $tags));
    }

    protected function getSearchOptions(array $data): array
    {
        $option = fn (string $key, mixed $default = null) => data_get($data, $key, $default);

        $id = Arr::wrap($option('id'));
        $type = Arr::wrap($option('type'));

        return [
            'id' => $this->getIds($id, $type),
            'tags' => Arr::wrap($option('tags')),
            'sort' => Arr::wrap($option('sort')),
            'query' => (string) $option('query', '*'),
            'limit' => (int) $option('size', 24),
        ];
    }

    protected function getIds(array $id, array $type): ?array
    {
        $models = $this->getVideoIdsByTypes($type);

        return array_merge($id, $models);
    }

    protected function getVideoIdsByTypes(?array $types = null): array
    {
        if (!$types) {
            return [];
        }

        return Video::cacheFor(60 * 10)
            ->select('id')
            ->withAnyType($types, auth()?->user())
            ->pluck('id')
            ->take(500)
            ->all();
    }
}
