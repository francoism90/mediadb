<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

trait InteractsWithHashids
{
    /**
     * @return string|int
     */
    public function getRouteKey()
    {
        return static::convertToHashid($this->getKey());
    }

    /**
     * @param string      $value
     * @param string|null $connection
     *
     * @return Model
     */
    public static function findByHashid(string $value, string $connection = null): Model
    {
        $key = static::convertHashidToId($value, $connection);

        $modelInstance = resolve($connection ?? get_called_class());

        return $modelInstance::find($key);
    }

    /**
     * @param string      $value
     * @param string|null $connection
     *
     * @return Model
     */
    public static function findByHashidOrFail(string $value, string $connection = null): Model
    {
        $key = static::convertHashidToId($value, $connection);

        $modelInstance = resolve($connection ?? get_called_class());

        return $modelInstance::findOrFail($key);
    }

    /**
     * @param mixed $values
     *
     * @return mixed
     */
    public static function convertHashidsToModels($values)
    {
        return collect($values)->map(function ($value) {
            if ($value instanceof Model) {
                return $value;
            }

            return static::findByHashid($value);
        });
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed                                 $hashids
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereHashids(Builder $query, $hashids)
    {
        $hashids = static::convertHashidsToModels($hashids);

        collect($hashids)->each(function ($model) use ($query) {
            $query->where('id', $model ? $model->id : 0);
        });

        return $query;
    }

    /**
     * @param string|null $connection
     *
     * @return Hashids
     */
    protected static function getHashidsConnection(string $connection = null)
    {
        return Hashids::connection($connection ?? get_called_class());
    }

    /**
     * @param int|string  $value
     * @param string|null $connection
     *
     * @return string|int
     */
    protected static function convertToHashid($value, string $connection = null)
    {
        return static::getHashidsConnection($connection)->encode($value);
    }

    /**
     * @param string      $value
     * @param string|null $connection
     *
     * @return string|int
     */
    protected static function convertHashidToId(string $value, string $connection = null)
    {
        $decoded = static::getHashidsConnection($connection)->decode($value);

        return $decoded[0] ?? 0;
    }
}
