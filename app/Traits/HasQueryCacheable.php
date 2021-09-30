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
        return config('api.cache_queries', 3600);
    }

    protected function getCacheBaseTags(): array
    {
        return [
            sprintf('query_cache_%s', $this->getTable())
        ];
    }

    protected function cacheTagsValue(): array
    {
        return [
            $this->getTable(),
            sprintf('%s:%s', $this->getTable(), $this->id)
        ];
    }

    protected function cachePrefixValue(): string
    {
        return sprintf('%s_', $this->getTable());
    }

    public function getCacheTagsToInvalidateOnUpdate($relation = null, $pivotedModels = null): array
    {
        return $this->cacheTagsValue();
    }
}
