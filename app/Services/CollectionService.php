<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class CollectionService
{
    /**
     * @param Model $model
     * @param array $collections
     *
     * @return void
     */
    public function sync(Model $model, ?array $collections = []): void
    {
        $collect = collect($collections);

        $types = config('collection.types');

        foreach ($types as $type) {
            $items = $collect
                ->where('type', $type)
                ->pluck('name')
                ->unique()
                ->toArray();

            $model->syncCollectionsWithType($items, $type);
        }
    }
}
