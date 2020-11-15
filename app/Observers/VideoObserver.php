<?php

namespace App\Observers;

use App\Models\Video;
use Spatie\ResponseCache\Facades\ResponseCache;

class VideoObserver
{
    /**
     * @param \App\Models\Video $model
     */
    public function created(Video $model)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Video $model
     */
    public function updated(Video $model)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Video $model
     */
    public function deleted(Video $model)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Video $model
     */
    public function restored(Video $model)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Video $model
     */
    public function forceDeleted(Video $model)
    {
        ResponseCache::clear();
    }
}
