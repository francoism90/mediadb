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
    public static function getTagsByTypeMap(array $tags = []): Collection
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

        $collect = self::getTagsByTypeMap($tags);

        foreach (['Genre', 'Language', 'Person'] as $type) {
            if ($collect->has($type)) {
                $this->syncTagsWithType(
                    $collect->get($type)->unique()->toArray(), $type
                );
            }
        }
    }
}
