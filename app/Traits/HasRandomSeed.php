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
     * @return int
     */
    public static function setRandomSeed(string $class = null, int $ttl = 1800): int
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
     * @param string $key
     *
     * @return int
     */
    public static function getRandomSeed(string $key = null): int
    {
        return Cache::get(self::getRandomSeedKey($key), 1000);
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
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInRandomSeedOrder(Builder $query)
    {
        return $query->inRandomOrder(
            self::getRandomSeed()
        );
    }
}
