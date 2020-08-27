<?php

namespace App\Services;

use App\Models\Collection;
use Illuminate\Support\LazyCollection;

class CollectionSyncService
{
    /**
     * @return void
     */
    public function sync(): void
    {
        $this->attachModelTagsToMedia();
    }

    /**
     * @return void
     */
    protected function attachModelTagsToMedia(): void
    {
        $models = $this->getCollectionsWithTags();

        foreach ($models as $model) {
            $tags = $model->tags->all();

            foreach ($model->videos as $video) {
                $video->attachTags($tags);
            }
        }
    }

    /**
     * @return LazyCollection
     */
    protected function getCollectionsWithTags(): LazyCollection
    {
        return Collection::has('tags')
            ->with(['tags', 'videos'])
            ->cursor();
    }
}
