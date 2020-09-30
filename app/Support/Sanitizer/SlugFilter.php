<?php

namespace App\Support\Sanitizer;

use Elegant\Sanitizer\Contracts\Filter;
use Illuminate\Support\Str;

class SlugFilter implements Filter
{
    public function apply($value, array $options = [])
    {
        return Str::slug($value);
    }
}
