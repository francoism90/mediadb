<?php

namespace App\Traits;

use Illuminate\Support\Carbon;

trait Viewable
{
    /**
     * @param string $collection
     * @param Carbon $expireAt
     *
     * @return void
     */
    public function recordView(string $collection = null, Carbon $expireAt)
    {
        views($this)
            ->collection($collection)
            ->cooldown($expireAt)
            ->record();
    }

    /**
     * @param string $key
     *
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
}
