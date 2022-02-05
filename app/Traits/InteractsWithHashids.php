<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Vinkla\Hashids\Facades\Hashids;

trait InteractsWithHashids
{
    public function getRouteKey(): string
    {
        return static::convertToHashid($this->getKey());
    }

    public static function findByHashid(string $value, ?string $connection = null): ?Model
    {
        $key = static::convertHashidToId($value, $connection);

        $model = app($connection ?? static::class);

        return $model::find($key);
    }

    public static function findByHashidOrFail(string $value, ?string $connection = null): ?Model
    {
        $key = static::convertHashidToId($value, $connection);

        $model = app($connection ?? static::class);

        return $model::findOrFail($key);
    }

    public static function getHashidsConnection(?string $connection = null): object
    {
        return Hashids::connection($connection ?? static::class);
    }

    public static function convertToHashid(string $value, ?string $connection = null): string
    {
        return static::getHashidsConnection($connection)?->encode($value);
    }

    public static function convertHashidToId(string $value, ?string $connection = null): string|int
    {
        $decoded = static::getHashidsConnection($connection)?->decode($value);

        return $decoded[0] ?? null;
    }

    public static function convertHashidsToModels(array|string $values): Collection
    {
        return collect($values)->map(function ($value) {
            if ($value instanceof Model) {
                return $value;
            }

            return static::findByHashid($value);
        });
    }

    public function scopeWhereHashids(Builder $query, array|string $hashids): Builder
    {
        $hashids = static::convertHashidsToModels($hashids);

        collect($hashids)
            ?->each(fn ($query, $model) => $query->where('id', $model ? $model->id : 0));

        return $query;
    }
}
