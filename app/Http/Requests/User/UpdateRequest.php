<?php

namespace App\Http\Requests\User;

use Elegant\Sanitizer\Laravel\SanitizesInput;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    use SanitizesInput;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'locale' => 'nullable|string|min:1|max:5|in:en,nl',
        ];
    }

    public function filters(): array
    {
        return [
            'locale' => 'trim|empty_string_to_null|strip_tags',
        ];
    }
}
