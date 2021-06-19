<?php

namespace App\Http\Requests\Tag;

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
            'name' => 'required|string|min:1|max:255',
            'type' => 'required|string|in:actor,genre,language,studio',
            'order_column' => 'nullable|int',
        ];
    }

    public function filters(): array
    {
        return [
            'name' => 'trim|strip_tags',
            'type' => 'trim|escape|lowercase',
            'order_column' => 'cast:int',
        ];
    }
}
