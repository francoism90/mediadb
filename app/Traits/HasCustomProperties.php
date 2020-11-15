<?php

namespace App\Traits;

trait HasCustomProperties
{
    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function fillCustomProperty(string $key, $default = null)
    {
        return data_fill($this->custom_properties, $key, $default);
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getCustomProperty(string $key, $default = null)
    {
        return data_get($this->custom_properties, $key, $default);
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function setCustomProperty(string $key, $value = null)
    {
        return data_set($this->custom_properties, $key, $value);
    }
}
