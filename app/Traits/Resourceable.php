<?php

namespace App\Traits;

trait Resourceable
{
    /**
     * @return array
     */
    public function getAppends(): array
    {
        return $this->appends;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public function hasField(string $value): bool
    {
        $fields = array_keys($this->getAppends());

        return in_array($value, $fields);
    }
}
