<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;

class TagService
{
    public const TAG_TYPES = [
        'actor',
        'genre',
        'language',
        'studio',
    ];

    /**
     * @param Model $model
     * @param array $tags
     *
     * @return void
     */
    public function sync(Model $model, array $tags = []): void
    {
        $collect = collect($tags);

        foreach (self::TAG_TYPES as $type) {
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
