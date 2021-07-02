<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait InteractsWithScout
{
    public function extractLeadingZeroes(int | string $value = null): ?array
    {
        // https://stackoverflow.com/a/18369334
        $str = (string) Str::of($value)->replaceMatches('/^(\D*)0*/', '');

        return array_unique([$value, $str]);
    }
}
