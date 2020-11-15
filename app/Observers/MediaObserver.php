<?php

namespace App\Observers;

use App\Models\Media;
use Spatie\ResponseCache\Facades\ResponseCache;

class MediaObserver
{
    /**
     * @param \App\Models\Media $model
     */
    public function created(Media $model)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Media $model
     */
    public function updated(Media $model)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Media $model
     */
    public function deleted(Media $model)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Media $model
     */
    public function restored(Media $model)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Media $model
     */
    public function forceDeleted(Media $model)
    {
        ResponseCache::clear();
    }
}
