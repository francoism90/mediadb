<?php

namespace App\Actions\Tag;

use App\Helpers\Arr;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;

class GetTagsFeed
{
    public function __invoke(array $data): Builder
    {
        $params = $this->getOptions($data);

        $user = auth()?->user() ?? null;

        return Tag::active()
            ->when($params['id'], fn ($builder, $ids) => $builder->whereIn('id', $ids))
            ->when($params['type'], fn ($builder, $types) => $builder->withAnyType($types, $user));
    }

    protected function getOptions(array $data): array
    {
        $option = fn (string $key, mixed $default = null) => data_get($data, $key, $default);

        return [
            'id' => Arr::convert($option('id')),
            'type' => Arr::convert($option('type', 'ordered')),
        ];
    }
}
