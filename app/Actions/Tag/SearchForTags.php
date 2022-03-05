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
            $params['query'],
            function (Indexes $meilisearch, string $query, array $options) use ($params) {
                $options = array_merge($options, Arr::only($params, ['limit', 'sort']));

                return $meilisearch->search($query, $options);
            }
        )
        ->when($params['id'], fn ($engine, $ids) => $engine->whereIn('id', $ids))
        ->when($params['type'], fn ($engine, $types) => $engine->whereIn('type', $types));
    }

    protected function getSearchOptions(array $data): array
    {
        $option = fn (string $key, mixed $default = null) => data_get($data, $key, $default);

        $id = Arr::wrap($option('id'));
        $type = Arr::wrap($option('type'));

        return [
            'id' => $this->getIds($id, $type),
            'type' => $this->getTypes($type),
            'sort' => Arr::wrap($option('sort', 'order:asc')),
            'query' => (string) $option('query', '*'),
            'limit' => (int) $option('size', 24),
        ];
    }

    protected function getIds(array $id, array $type): ?array
    {
        $models = $this->getTagIdsByTypes($type);

        return array_merge($id, $models);
    }

    protected function getTypes(array $type): array
    {
        return Arr::only($type, config('tag.types'));
    }

    protected function getTagIdsByTypes(?array $types = null): array
    {
        // Skip indexed types
        $types = Arr::except($types, config('tag.types'));

        if (!$types) {
            return [];
        }

        return Tag::cacheFor(60 * 10)
            ->select('id')
            ->withAnyType($types, auth()?->user())
            ->pluck('id')
            ->take(500)
            ->all();
    }
}
