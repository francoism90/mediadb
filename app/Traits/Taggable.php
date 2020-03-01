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
            return [$item['type'] ?? 'default' => $item['name']];
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

        foreach (['default', 'category', 'language', 'people'] as $type) {
            $tags = $collect->has($type) ? $collect->get($type)->unique()->toArray() : [];

            $this->syncTagsWithType($tags, 'default' === $type ? null : $type);
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
