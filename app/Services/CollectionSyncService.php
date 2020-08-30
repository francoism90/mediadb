<?php

namespace App\Services;

use App\Models\Collection as Collection;
use Illuminate\Support\LazyCollection;

class CollectionSyncService
{
    /**
     * @return void
     */
    public function sync(): void
    {
        $this->attachVideoTagsToMedia();
    }

    /**
     * @return void
     */
    protected function attachVideoTagsToMedia(): void
    {
        $models = $this->getVideosWithTags();

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
    protected function getVideosWithTags(): LazyCollection
    {
        return Collection::has('tags')
            ->with(['tags', 'videos'])
            ->cursor();
    }
}
