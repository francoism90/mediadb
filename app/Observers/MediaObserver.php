<?php

namespace App\Observers;

use App\Models\Media;
use Spatie\ResponseCache\Facades\ResponseCache;

class MediaObserver
{
    /**
     * @param \App\Models\Media $media
     */
    public function created(Media $media)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Media $media
     */
    public function updated(Media $media)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Media $media
     */
    public function deleted(Media $media)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Media $media
     */
    public function restored(Media $media)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Media $media
     */
    public function forceDeleted(Media $media)
    {
        ResponseCache::clear();
    }
}
