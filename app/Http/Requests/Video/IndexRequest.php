<?php

namespace App\Http\Requests\Video;

use App\Models\Video;
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
            'filter.query' => 'nullable|string|min:1|max:255',
            // 'page' => 'nullable|array',
            'sort' => 'nullable|string|in:created:desc,duration:desc,duration:asc',
        ];
    }

    public function filters(): array
    {
        return [
            'filter' => 'trim|empty_string_to_null',
            'filter.query' => 'trim|strip_tags',
            // 'page' => 'trim|empty_string_to_null',
            'sort' => 'trim|strip_tags',
        ];
    }
}
