<?php

namespace App\Traits;

use App\Jobs\ProcessView;

trait Viewable
{
    /**
     * @return int
     */
    public function getViewsAttribute(): int
    {
        return views($this)->remember()->unique()->count();
    }

    /**
     * @param string      $collection
     * @param string|null $cooldown
     *
     * @return void
     */
    public function recordView(string $collection = null, $cooldown = null): void
    {
        $visitor = collect([
            'ipAddress' => request()->ip(),
            'visitorId' => auth()->user()->id,
        ]);

        ProcessView::dispatch($this, $visitor, $collection, $cooldown);
    }
}
