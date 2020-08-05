<?php

namespace App\Services;

use App\Models\Channel;
use Illuminate\Support\Collection;

class ChannelSyncService
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
        $models = $this->getModelsWithTags();

        foreach ($models as $model) {
            $tags = $model->tags->all();

            foreach ($model->media as $media) {
                $media->attachTags($tags);
            }
        }
    }

    /**
     * @return Collection
     */
    protected function getModelsWithTags(): Collection
    {
        $collection = Channel::has('tags')
            ->with(['media', 'tags'])
            ->get();

        return $collection;
    }
}
