<?php

namespace App\Rules;

use App\Models\Tag;
use Illuminate\Contracts\Validation\Rule;

class IsExistingTag implements Rule
{
    /**
     * @param string $attribute
     * @param mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        return Tag::findByHashid($value ?? null)->exists();
    }

    public function message(): string
    {
        return 'The given tag(s) does not exists.';
    }
}
