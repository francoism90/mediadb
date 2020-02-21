<?php

namespace App\Traits;

use App\Jobs\ProcessView;

trait Viewable
{
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
