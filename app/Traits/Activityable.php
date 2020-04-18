<?php

namespace App\Traits;

trait Activityable
{
    /**
     * @param string $log
     * @param array  $properties
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
