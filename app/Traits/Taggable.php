<?php

namespace App\Traits;

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
            return [$item['type'] => $item['name']];
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

        // Sync with types
        $collect = self::getTagsTypeMapped($tags);

        foreach (['category', 'language', 'people'] as $type) {
            $tags = $collect->has($type) ? $collect->get($type)->unique()->toArray() : [];

            $this->syncTagsWithType($tags, $type);
        }
    }
}
