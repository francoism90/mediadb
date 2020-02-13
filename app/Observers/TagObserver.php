<?php

namespace App\Observers;

use App\Models\Tag;
use Spatie\ResponseCache\Facades\ResponseCache;

class TagObserver
{
    /**
     * @param \App\Models\Tag $Tag
     */
    public function created(Tag $Tag)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Tag $Tag
     */
    public function updated(Tag $Tag)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Tag $Tag
     */
    public function deleted(Tag $Tag)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Tag $Tag
     */
    public function restored(Tag $Tag)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Tag $Tag
     */
    public function forceDeleted(Tag $Tag)
    {
        ResponseCache::clear();
    }
}
