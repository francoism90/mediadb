<?php

namespace App\Traits;

trait Activityable
{
    /**
     * @param string $collection
     * @param Carbon $expireAt
     *
     * @return void
     */
    public function recordActivity(string $log, array $properties = null)
    {
        activity()
            ->performedOn($this)
            ->causedBy(auth()->user())
            ->withProperties($properties)
            ->log($log);
    }
}
