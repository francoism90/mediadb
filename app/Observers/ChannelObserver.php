<?php

namespace App\Observers;

use App\Models\Channel;
use Spatie\ResponseCache\Facades\ResponseCache;

class ChannelObserver
{
    /**
     * @param \App\Models\Channel $channel
     */
    public function created(Channel $channel)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Channel $channel
     */
    public function updated(Channel $channel)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Channel $channel
     */
    public function deleted(Channel $channel)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Channel $channel
     */
    public function restored(Channel $channel)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Channel $channel
     */
    public function forceDeleted(Channel $channel)
    {
        ResponseCache::clear();
    }
}
