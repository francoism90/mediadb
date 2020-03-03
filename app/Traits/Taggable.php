<?php

namespace App\Traits;

use App\Models\Tag;
use Illuminate\Support\Collection;

trait Taggable
{
    /**
     * @param array $tags
     *
     * @return Collection
     */
    public static function getTagsTypeMapped(array $tags = []): Collection
    {
        return collect($tags)->mapToGroups(function ($item) {
            $tag = Tag::findFromStringOfAnyType($item['name']);
            $type = $tag['type'] ?? $item['type'] ?? 'null';
            $name = $tag['name'] ?? $item['name'];

            return [$type => $name];
        });
    }

    /**
     * @param array $tags
     *
     * @return void
     */
    public function syncTagsWithTypes(array $tags = [])
    {
        // Assume all tags should be removed
        if (!$tags) {
            return $this->syncTags(null);
        }

        // Sync with types (if any)
        $collect = self::getTagsTypeMapped($tags);

        foreach (['null', 'category', 'language', 'people'] as $type) {
            $tags = $collect->has($type) ? $collect->get($type)->unique()->toArray() : [];

            $this->syncTagsWithType($tags, 'null' === $type ? null : $type);
        }

        // Order tags by name
        $this->setTagsOrderByName();
    }

    /**
     * @return void
     */
    public function setTagsOrderByName()
    {
        $tags = Tag::pluck('name', 'id')->toArray();

        // Sort case-insensitive
        natcasesort($tags);

        Tag::setNewOrder(array_keys($tags));
    }
}
