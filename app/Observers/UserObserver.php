<?php

namespace App\Observers;

use App\Models\User;
use Spatie\ResponseCache\Facades\ResponseCache;

class UserObserver
{
    /**
     * @param \App\Models\User $model
     */
    public function created(User $model)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\User $model
     */
    public function updated(User $model)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\User $model
     */
    public function deleted(User $model)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\User $model
     */
    public function restored(User $model)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\User $model
     */
    public function forceDeleted(User $model)
    {
        ResponseCache::clear();
    }
}
