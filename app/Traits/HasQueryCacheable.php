<?php

namespace App\Traits;

use Rennokki\QueryCache\Traits\QueryCacheable;

trait HasQueryCacheable
{
    use QueryCacheable;

    /**
     * @var bool
     */
    protected static $flushCacheOnUpdate = true;

    protected function cacheForValue(): int
    {
        return config('api.cache_queries', 0);
    }

    protected function getCacheBaseTags(): array
    {
        return [
            $this->getTable(),
            sprintf('%s:%d', $this->getTable(), $this->id),
        ];
    }

    protected function cachePrefixValue(): string
    {
        return sprintf('%s_', $this->getTable());
    }

    public function getCacheTagsToInvalidateOnUpdate($relation = null, $pivotedModels = null): array
    {
        return $this->getCacheBaseTags();
    }
}
