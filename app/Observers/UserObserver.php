<?php

namespace App\Observers;

use App\Models\User;
use Spatie\ResponseCache\Facades\ResponseCache;

class UserObserver
{
    /**
     * @param \App\Models\User $user
     */
    public function created(User $user)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\User $user
     */
    public function updated(User $user)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\User $user
     */
    public function deleted(User $user)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\User $user
     */
    public function restored(User $user)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\User $user
     */
    public function forceDeleted(User $user)
    {
        ResponseCache::clear();
    }
}
