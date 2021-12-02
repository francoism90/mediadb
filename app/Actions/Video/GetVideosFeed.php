<?php

namespace App\Actions\Video;

use App\Models\Video;
use Illuminate\Database\Eloquent\Builder;

class GetVideosFeed
{
    public function __invoke(array $data): Builder
    {
        $params = $this->getOptions($data);
        logger($params);

        $user = auth()?->user() ?? null;

        return Video::active()
            ->when($params['id'], fn ($builder, $ids) => $builder->whereIn('id', $ids))
            ->withAnyType($params['type'], $user);
    }

    protected function getOptions(array $data): array
    {
        $option = fn (string $key, mixed $default = null) => data_get($data, $key, $default);

        return [
            'id' => $option('id'),
            'type' => $option('type', 'random'),
        ];
    }
}
