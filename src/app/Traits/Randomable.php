<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait Randomable
{
    public static function bootRandomable()
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

        return Cache::remember(self::getRandomSeedKey($class), $ttl, function () {
            return mt_rand(1000, 9500);
        });
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
     * @param QueryBuilder $query
     *
     * @return Collection
     */
    public function scopeRandomSeed($query)
    {
        return $query->inRandomOrder(self::getRandomSeed());
    }
}
