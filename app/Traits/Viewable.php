<?php

namespace App\Traits;

use App\Jobs\ProcessView;

trait Viewable
{
    /**
     * @return void
     */
    public function recordView(string $collection = null): void
    {
        $visitor = collect([
            'ipAddress' => request()->ip(),
            'visitorId' => auth()->user()->id,
        ]);

        ProcessView::dispatch($this, $visitor, $collection)
            ->delay(now()->addMinutes(30));
    }
}
