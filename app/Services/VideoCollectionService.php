<?php

namespace App\Services;

use App\Models\Video;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class VideoCollectionService
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
     * @param Video      $video
     * @param Collection $collections
     * @param bool       $detach
     *
     * @return void
     */
    public function sync(
        Model $model,
        Video $video,
        Collection $collections,
        bool $detach = true
    ): void {
        // Create collections for model
        $videoCollections = $this->collectionService->create($model, $collections);

        // Get the all model collections
        $modelCollections = $model->collections()->with('videos')->get();

        // Loop over all collections
        foreach ($modelCollections as $collection) {
            $hasVideo = $collection->videos->firstWhere('id', $video->id);
            $inCollection = $videoCollections->firstWhere('id', $collection->id);

            // Sync collections
            if (!$hasVideo && $inCollection) {
                $collection->videos()->attach($video->id);
            } elseif ($detach && $hasVideo && !$inCollection) {
                $collection->videos()->detach($video->id);
            }
        }
    }
}
