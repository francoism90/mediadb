<?php

namespace App\Observers;

use App\Models\Collection;
use Spatie\ResponseCache\Facades\ResponseCache;

class CollectionObserver
{
    /**
     * @param \App\Models\Collection $model
     */
    public function created(Collection $model)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Collection $model
     */
    public function updated(Collection $model)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Collection $model
     */
    public function deleted(Collection $model)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Collection $model
     */
    public function restored(Collection $model)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Collection $model
     */
    public function forceDeleted(Collection $model)
    {
        ResponseCache::clear();
    }
}
