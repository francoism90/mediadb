<?php

namespace App\Traits;

trait Viewable
{
    /**
     * @return int
     */
    public function getViewsAttribute(): int
    {
        return views($this)
            ->remember()
            ->collection('view_count')
            ->unique()
            ->count();
    }

    /**
     * @param string      $collection
     * @param string|null $cooldown
     *
     * @return void
     */
    public function recordView(string $collection = null, string $cooldown = null): void
    {
        views($this)
            ->delayInSession($cooldown ?? now()->addHour())
            ->overrideIpAddress(request()->ip())
            ->overrideVisitor(auth()->user()->id ?? 0)
            ->collection($collection)
            ->record();
    }
}
