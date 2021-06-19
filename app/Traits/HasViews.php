<?php

namespace App\Traits;

use Illuminate\Support\Carbon;

trait HasViews
{
    public function recordView(string $collection = null, Carbon $expireAt): void
    {
        views($this)
            ->collection($collection)
            ->cooldown($expireAt)
            ->record();
    }

    public function getViewsAttribute(): int
    {
        return views($this)
            ->remember(now()->addHours(6))
            ->collection('view_count')
            ->unique()
            ->count();
    }
}
