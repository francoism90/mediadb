<?php

namespace App\Rules;

use App\Models\Media;
use Illuminate\Contracts\Validation\Rule;

class IsUniqueMedia implements Rule
{
    /**
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Used by spatie/medialibrary
        $fileName = str_replace(
            ['#', '/', '\\', ' '],
            '-',
            $value->getClientOriginalName()
        );

        return 0 === Media::where('file_name', $fileName)
            ->where('mime_type', $value->getMimeType())
            ->where('size', $value->getSize())
            ->count();
    }

    /**
     * @return string
     */
    public function message()
    {
        return 'This file has already been uploaded.';
    }
}
