<?php

namespace App\Actions\Tag;

use Illuminate\Database\Eloquent\Model;

class SyncWithTypes
{
    public function execute(Model $model, array $tags = []): void
    {
        $collect = collect($tags);

        $types = $this->getTypes();

        foreach ($types as $type) {
            $items = $collect
                ->where('type', $type)
                ->pluck('name')
                ->unique()
                ->toArray();

            $model->syncTagsWithType($items, $type);
        }
    }

    protected function getTypes(): ?array
    {
        return config('api.tag_types');
    }
}
