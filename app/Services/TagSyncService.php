<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class TagSyncService
{
    /**
     * @param Model      $model
     * @param array|null $tags
     *
     * @return void
     */
    public function sync(Model $model, ?array $tags = []): void
    {
        // Remove tags as we do type mapping
        $model->syncTags(null);

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
     * @return void
     */
    public function sortTagsByName(): void
    {
        $tags = Tag::pluck('name', 'id')->toArray();

        // Sort case-insensitive
        natcasesort($tags);

        Tag::setNewOrder(
            array_keys($tags)
        );
    }

    /**
     * @param array|null $tags
     *
     * @return Collection
     */
    protected function getMappedByType(?array $tags = []): Collection
    {
        if (!$tags) {
            return collect();
        }

        return collect($tags)->mapToGroups(
            fn ($tag) => [$tag['type'] ?? null => $tag['name']]
        );
    }
}
