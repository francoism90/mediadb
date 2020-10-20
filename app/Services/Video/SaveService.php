<?php

namespace App\Services\Video;

use App\Models\User;
use App\Models\Video;
use App\Services\Collection\SyncService;

class SaveService
{
    /**
     * @var SyncService
     */
    protected $syncService;

    public function __construct(SyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * @param User  $user
     * @param Video $video
     * @param array $collections
     * @param bool  $detach
     *
     * @return void
     */
    public function sync(
        User $user,
        Video $video,
        array $collections,
        bool $detach = false
    ): void {
        // Create collections
        $models = $this->syncService->create($user, $collections);

        // Get the current user collections
        $userCollections = $user->collections()->with('videos:id')->get();

        // Sync each collection
        foreach ($userCollections as $collection) {
            $hasVideo = $collection->videos->firstWhere('id', $video->id);
            $attachVideo = $models->firstWhere('id', $collection->id);

            if (!$hasVideo && $attachVideo) {
                $collection->videos()->attach($video->id);
            } elseif ($hasVideo && !$attachVideo && $detach) {
                $collection->videos()->detach($video->id);
            }
        }
    }
}
