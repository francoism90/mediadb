<?php

namespace App\Observers;

use App\Models\Video;
use Spatie\ResponseCache\Facades\ResponseCache;

class VideoObserver
{
    /**
     * @param \App\Models\Video $video
     */
    public function created(Video $video)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Video $video
     */
    public function updated(Video $video)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Video $video
     */
    public function deleted(Video $video)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Video $video
     */
    public function restored(Video $video)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Video $video
     */
    public function forceDeleted(Video $video)
    {
        ResponseCache::clear();
    }
}
