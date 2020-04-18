<?php

namespace App\Rules;

use App\Models\Tag;
use Illuminate\Contracts\Validation\Rule;

class IsExistingTag implements Rule
{
    /**
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Tag::findByHash($value['id'] ?? null)->exists();
    }

    /**
     * @return string
     */
    public function message()
    {
        return 'The given tag does not exists.';
    }
}
