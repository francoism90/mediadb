<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class MediaCollectionService
{
    /**
     * @var CollectionService
     */
    protected $collectionService;

    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

    /**
     * @param Model      $model
     * @param Media      $media
     * @param Collection $collections
     * @param array      $attributes
     *
     * @return void
     */
    public function sync(
        Model $model,
        Media $media,
        Collection $collections,
        array $attributes = []
    ): void {
        // Create collections for model
        $mediaCollection = $this->collectionService->create(
            $model,
            $collections
        );

        // Get the all model collections with media
        $currentCollections = $model->collections()->with('media')->get();

        // Sync to all collections
        foreach ($currentCollections as $collection) {
            // Current collection has media
            $hasMedia = $collection->media->firstWhere('id', $media->id);

            // Created collection has media
            $wantsMedia = $mediaCollection->firstWhere('id', $collection->id);

            // Attach or detach from collection
            if (!$hasMedia && $wantsMedia) {
                $collection->media()->attach([$media->id => $attributes])->save();
            } elseif ($hasMedia && !$wantsMedia) {
                $collection->media()->detach([$media->id])->save();
            }
        }
    }
}
