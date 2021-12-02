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
}
