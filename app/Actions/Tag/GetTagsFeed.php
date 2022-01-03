<?php

namespace App\Actions\Tag;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class GetTagsFeed
{
    public function __invoke(array $data): Builder
    {
        $params = $this->getOptions($data);

        $user = auth()?->user() ?? null;

        return Tag::cacheFor(60 * 10)
            ->when($params['id'], fn ($builder, $ids) => $builder->whereIn('id', $ids))
            ->when($params['type'], fn ($builder, $types) => $builder->withAnyType($types, $user));
    }

    protected function getOptions(array $data): array
    {
        $option = fn (string $key, mixed $default = null) => data_get($data, $key, $default);

        return [
            'id' => Arr::wrap($option('id')),
            'type' => Arr::wrap($option('type', 'ordered')),
        ];
    }
}
