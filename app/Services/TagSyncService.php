<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class TagSyncService
{
    /**
     * @param Model $model
     * @param array $tags
     *
     * @return void
     */
    public function sync(Model $model, array $tags = []): void
    {
        // Assume all tags should be removed
        if (!$tags) {
            $model->syncTags(null);

            return;
        }

        // Get tags mapped by type
        $tagsByType = $this->getMappedByType($tags);

        foreach ($tagsByType as $type => $name) {
            $uniqueTags = $tagsByType->get($type)->unique()->toArray();

            $model->syncTagsWithType($uniqueTags, $type);
        }

        // Order tags by name
        $this->sortTagsByName();
    }

    /**
     * @param array $tags
     *
     * @return Collection
     */
    protected function getMappedByType(array $tags = []): Collection
    {
        return collect($tags)->mapToGroups(
            fn ($tag) => [$tag['type'] ?? null => $tag['name']]
        );
    }

    /**
     * @return void
     */
    protected function sortTagsByName(): void
    {
        $tags = Tag::pluck('name', 'id')->toArray();

        // Sort case-insensitive
        natcasesort($tags);

        Tag::setNewOrder(
            array_keys($tags)
        );
    }
}
