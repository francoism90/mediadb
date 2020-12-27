<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

trait InteractsWithHashids
{
    /**
     * @param string|null $connection
     *
     * @return Hashids
     */
    public static function getHashidsConnection(string $connection = null)
    {
        return Hashids::connection($connection ?? get_called_class());
    }

    /**
     * @return string|int
     */
    public function getRouteKey()
    {
        return self::getEncodedHash($this->getKey());
    }

    /**
     * @return string|int
     */
    public function getDecodedRouteKey()
    {
        return self::getDecodedHash($this->getKey());
    }

    /**
     * @param string      $key
     * @param string|null $connection
     *
     * @return string|int
     */
    public static function getDecodedHash(string $key, string $connection = null)
    {
        $decoded = self::getHashidsConnection($connection)->decode($key);

        return $decoded[0] ?? null;
    }

    /**
     * @param int|string  $key
     * @param string|null $connection
     *
     * @return string|int
     */
    public static function getEncodedHash($key, string $connection = null)
    {
        return self::getHashidsConnection($connection)->encode($key);
    }

    /**
     * @param string      $key
     * @param string|null $connection
     *
     * @return Model
     */
    public static function findByHash(string $key, string $connection = null): Model
    {
        $key = self::getDecodedHash($key, $connection);

        $modelInstance = resolve($connection ?? get_called_class());

        return $modelInstance::findOrFail($key);
    }
}
