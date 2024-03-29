<?php

namespace App\Http\Requests\Tag;

use Elegant\Sanitizer\Laravel\SanitizesInput;
use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    use SanitizesInput;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => 'nullable|numeric|min:1|max:48',
            'size' => 'nullable|numeric|min:1|max:24',
            'type' => 'nullable|string|in:actor,genre,language,studio',
            'query' => 'nullable|string|min:1|max:255',
            'sort' => 'nullable|string|in:name:asc,name:desc,items:asc,items:desc',
        ];
    }

    public function filters(): array
    {
        return [
            'page' => 'trim|empty_string_to_null',
            'size' => 'trim|empty_string_to_null',
            'type' => 'trim|empty_string_to_null',
            'query' => 'trim|empty_string_to_null',
            'sort' => 'trim|empty_string_to_null',
        ];
    }
}
