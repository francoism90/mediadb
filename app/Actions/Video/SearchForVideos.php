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
        ->when($params['tags'], fn ($builder, $tags) => $builder->whereIn('tags', $tags));
    }

    protected function getSearchOptions(array $data): array
    {
        $option = fn (string $key, mixed $default = null) => data_get($data, $key, $default);

        $types = $this->getTypeFilter($option('type'));

        $ids = $this->getIdFilter($option('id'), $types);

        return [
            'id' => $ids,
            'query' => $option('query'),
            'tags' => (array) $option('tags'),
            'limit' => (int) $option('size', 24),
        ];
    }

    protected function getIdFilter(mixed $ids = null, mixed $types = null): array
    {
        return array_merge(
            $ids ?? [],
            $this->getIdsByType($types),
        );
    }

    protected function getTypeFilter(mixed $types = null): array
    {
        $types = $this->convertToArray($types);

        return $types;
    }

    protected function getIdsByType(?array $types = null): ?array
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

    protected function convertToArray(mixed $value = null): array
    {
        return is_string($value) ? explode(',', $value) : (array) $value;
    }
}
