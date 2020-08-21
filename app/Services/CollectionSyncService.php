<?php

namespace App\Services;

use App\Models\Collection;
use Illuminate\Support\Collection as IlluminateCollection;

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
     * @return IlluminateCollection
     */
    protected function getCollectionsWithTags(): IlluminateCollection
    {
        return Collection::has('tags')
            ->with(['tags', 'videos'])
            ->get();
    }
}
