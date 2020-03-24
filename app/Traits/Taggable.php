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
            return [$item['type'] ?? null => $item['name']];
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
        $types = self::getTagsTypeMapped($tags);

        foreach ($types as $type => $name) {
            $tags = $types->get($type)->unique()->toArray();

            $this->syncTagsWithType($tags, $type);
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
