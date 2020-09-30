<?php

namespace App\Services\Video;

use App\Models\User;
use App\Models\Video;
use App\Services\Collection\SyncService as CollectionSyncService;
use Illuminate\Support\Collection;

class SaveService
{
    /**
     * @var CollectionSyncService
     */
    protected $collectionService;

    public function __construct(CollectionSyncService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

    /**
     * @param User       $user
     * @param Video      $video
     * @param Collection $collections
     * @param bool       $detach
     *
     * @return void
     */
    public function sync(
        User $user,
        Video $video,
        Collection $collections,
        bool $detach = true
    ): void {
        // Create collections for model
        $videoCollections = $this->collectionService->create($user, $collections);

        // Get the user collections
        $userCollections = $user->collections()->with('videos')->get();

        // Loop over all collections
        foreach ($userCollections as $collection) {
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
