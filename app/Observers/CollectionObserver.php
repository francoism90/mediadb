<?php

namespace App\Observers;

use App\Models\Collection;
use Spatie\ResponseCache\Facades\ResponseCache;

class CollectionObserver
{
    /**
     * @param \App\Models\Collection $Collection
     */
    public function created(Collection $Collection)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Collection $Collection
     */
    public function updated(Collection $Collection)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Collection $Collection
     */
    public function deleted(Collection $Collection)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Collection $Collection
     */
    public function restored(Collection $Collection)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Collection $Collection
     */
    public function forceDeleted(Collection $Collection)
    {
        ResponseCache::clear();
    }
}
