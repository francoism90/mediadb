<?php

namespace App\Http\Requests\Video;

use Elegant\Sanitizer\Laravel\SanitizesInput;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\ValidationRules\Rules\Delimited;

class IndexRequest extends FormRequest
{
    use SanitizesInput;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tagRule = (new Delimited('string'))->max(10)->allowDuplicates();

        return [
            'page' => 'nullable|numeric|min:1|max:48',
            'size' => 'nullable|numeric|min:1|max:24',
            'tags' => [$tagRule],
            'type' => 'nullable|string|in:favorites,following,viewed',
            'query' => 'nullable|string|min:1|max:255',
            'sort' => 'nullable|string|in:name:asc,duration:asc,duration:desc,created:desc,created:asc',
        ];
    }

    public function filters(): array
    {
        return [
            'page' => 'trim|empty_string_to_null',
            'size' => 'trim|empty_string_to_null',
            'tags' => 'trim|empty_string_to_null',
            'type' => 'trim|empty_string_to_null',
            'query' => 'trim|empty_string_to_null',
            'sort' => 'trim|empty_string_to_null',
        ];
    }
}
