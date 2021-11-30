<?php

namespace App\Http\Requests\Video;

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
            'filter' => 'nullable|array',
            'filter.type' => 'nullable|string|in:favorites,following,viewed',
            'filter.query' => 'nullable|string|min:1|max:255',
            'page' => 'nullable|numeric',
            'size' => 'nullable|numeric',
            'sort' => 'nullable|string|in:created:desc,duration:desc,duration:asc',
        ];
    }

    public function filters(): array
    {
        return [
            'filter' => 'trim|empty_string_to_null',
            'filter.type' => 'trim|empty_string_to_null',
            'filter.query' => 'trim|empty_string_to_null',
            'page' => 'trim|empty_string_to_null',
            'size' => 'trim|empty_string_to_null',
            'sort' => 'trim|empty_string_to_null',
        ];
    }
}
