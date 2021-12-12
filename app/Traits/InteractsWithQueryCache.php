<?php

namespace App\Traits;

use Rennokki\QueryCache\Traits\QueryCacheable;

trait InteractsWithQueryCache
{
    use QueryCacheable;

    /**
     * @var bool
     */
    protected static $flushCacheOnUpdate = true;
}
