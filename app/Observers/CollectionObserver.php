<?php

namespace App\Observers;

use App\Models\Collection;
use Spatie\ResponseCache\Facades\ResponseCache;

class CollectionObserver
{
    /**
     * @param \App\Models\Collection $collection
     */
    public function created(Collection $collection)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Collection $collection
     */
    public function updated(Collection $collection)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Collection $collection
     */
    public function deleted(Collection $collection)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Collection $collection
     */
    public function restored(Collection $collection)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Collection $collection
     */
    public function forceDeleted(Collection $collection)
    {
        ResponseCache::clear();
    }
}
