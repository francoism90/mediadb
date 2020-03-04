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
    public function hasAppend(string $value): bool
    {
        return in_array($value, $this->getAppends());
    }
}
