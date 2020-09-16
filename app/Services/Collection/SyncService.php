<?php

namespace App\Services\Collection;

use App\Models\Collection as CollectionModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

class SyncService
{
    public const COLLECTION_STATUS = 'private';

    /**
     * @return void
     */
    public function sync(): void
    {
        $this->attachVideoTagsToMedia();
    }

    /**
     * @param Model      $model
     * @param Collection $collections
     *
     * @return Collection
     */
    public function create(
        Model $model,
        Collection $collections
    ): Collection {
        $models = collect();

        foreach ($collections as $collection) {
            $name = $collection['name'] ?? $model->name;
            $type = $collection['type'] ?? null;
            $status = $collection['status'] ?? self::COLLECTION_STATUS;

            $collectionModel = $model->collections()->firstOrCreate(
                ['name' => $name],
                ['name' => $name, 'type' => $type]
            );

            if (!$collectionModel->status()) {
                $collectionModel->setStatus($status);
            }

            $models->push($collectionModel);
        }

        return $models;
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
        return CollectionModel::has('tags')
            ->with(['tags', 'videos'])
            ->cursor();
    }
}
