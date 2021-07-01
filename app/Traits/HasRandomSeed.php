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

    public static function setRandomSeed(?string $class = null, int $ttl = 1800): string | int
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

    public static function getRandomSeedKey(?string $class = null): string | int
    {
        $class = class_basename($class ?? static::class);

        return sprintf('randomSeed%s', $class);
    }

    public static function getRandomSeed(?string $class = null): string | int
    {
        return Cache::get(self::getRandomSeedKey($class), 1000);
    }

    public function scopeInRandomSeedOrder(Builder $query, ?string $class = null): Builder
    {
        return $query->inRandomOrder(
            self::getRandomSeed($class)
        );
    }
}
