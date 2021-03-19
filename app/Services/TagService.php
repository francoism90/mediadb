<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;

class TagService
{
    /**
     * @param Model $model
     * @param array $tags
     *
     * @return void
     */
    public function sync(Model $model, ?array $tags = []): void
    {
        $collect = collect($tags);

        $types = config('tags.types');

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
        $tags = Tag::all()->sortBy('name', SORT_NATURAL);

        Tag::setNewOrder(
            $tags->pluck('id')->toArray()
        );
    }
}
