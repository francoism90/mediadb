<?php

namespace App\Support\Sanitizer;

use Illuminate\Support\Str;
use Waavi\Sanitizer\Contracts\Filter;

class SlugifyFilter implements Filter
{
    public function apply($value, $options = [])
    {
        return Str::slug($value);
    }
}
