<?php

namespace App\Observers;

use App\Models\Playlist;
use Spatie\ResponseCache\Facades\ResponseCache;

class PlaylistObserver
{
    /**
     * @param \App\Models\Playlist $playlist
     */
    public function created(Playlist $playlist)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Playlist $playlist
     */
    public function updated(Playlist $playlist)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Playlist $playlist
     */
    public function deleted(Playlist $playlist)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Playlist $playlist
     */
    public function restored(Playlist $playlist)
    {
        ResponseCache::clear();
    }

    /**
     * @param \App\Models\Playlist $playlist
     */
    public function forceDeleted(Playlist $playlist)
    {
        ResponseCache::clear();
    }
}
