<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;

class TagService
{
    /**
     * @param Model $model
     * @param array $tags
     * @param array $types
     *
     * @return void
     */
    public function sync(
        Model $model,
        array $tags = [],
        array $types = ['actor', 'genre', 'studio']
    ): void {
        $collect = collect($tags);

        foreach ($types as $type) {
            $items = $collect
                ->where('type', $type)
                ->pluck('name')
                ->unique()
                ->toArray();

            $model->syncTagsWithType($items, $type);
        }
    }

    /**
     * @return void
     */
    public function sortByName(): void
    {
        $tags = Tag::select(['id', 'name'])
            ->pluck('name', 'id')
            ->toArray();

        natcasesort($tags);

        Tag::setNewOrder(
            array_keys($tags)
        );
    }
}
