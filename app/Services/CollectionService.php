<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class CollectionService
{
    /**
     * @param Model $model
     * @param array $collections
     * @param array $types
     *
     * @return void
     */
    public function sync(
        Model $model,
        array $collections = [],
        array $types = ['title']
    ): void {
        $collect = collect($collections);

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
