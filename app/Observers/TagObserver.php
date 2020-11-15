<?php

namespace App\Observers;

use App\Models\Tag;
use Spatie\ResponseCache\Facades\ResponseCache;

class TagObserver
{
    /**
     * @param \App\Models\Tag $model
     */
    public function created(Tag $model)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Tag $model
     */
    public function updated(Tag $model)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Tag $model
     */
    public function deleted(Tag $model)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Tag $model
     */
    public function restored(Tag $model)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Tag $model
     */
    public function forceDeleted(Tag $model)
    {
        ResponseCache::clear();
    }
}
