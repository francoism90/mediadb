<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

trait HasRandomSeed
{
    public static function bootHasRandomSeed(): void
    {
        self::setRandomSeed();
    }

    /**
     * @param string $class
     * @param int    $ttl
     *
     * @return string|int
     */
    public static function setRandomSeed(string $class = null, int $ttl = 1800)
    {
        if (method_exists(static::class, 'getRandomSeedLifetime')) {
            $ttl = parent::getRandomSeedLifetime();
        }

        return Cache::remember(
            self::getRandomSeedKey($class),
            $ttl,
            fn () => mt_rand(1000, 9500)
        );
    }

    /**
     * @param string $class
     *
     * @return string
     */
    public static function getRandomSeedKey(string $class = null): string
    {
        $key = class_basename($class ?? static::class);

        return "randomSeed{$key}";
    }

    /**
     * @param string $key
     *
     * @return string|int
     */
    public static function getRandomSeed(string $key = null)
    {
        return Cache::get(self::getRandomSeedKey($key), 1000);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeInRandomSeedOrder(Builder $query): Builder
    {
        $seedKey = self::getRandomSeedKey();

        return $query
            ->inRandomOrder(
                self::getRandomSeed()
            )
            ->cacheFor($this->cacheFor ?? 60 * 60)
            ->cacheTags(["{$seedKey}:randomize"]);
    }
}
