<?php

namespace App\Helpers;

class Arr
{
    public static function convert(mixed $obj = null): array
    {
        if (is_object($obj)) {
            return get_object_vars($obj);
        }

        return is_string($obj) ? explode(',', $obj) : (array) $obj;
    }

    /**
     * @doc https://stackoverflow.com/a/27295765
     */
    public static function parts(float $number, int $parts): array
    {
        return array_map('round', array_slice(range(0, $number, $number / $parts), 1));
    }
}
