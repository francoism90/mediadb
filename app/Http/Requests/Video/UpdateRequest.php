<?php

namespace App\Http\Requests\Video;

use App\Rules\IsExistingTag;
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
            'season_number' => 'nullable|string|min:1|max:255',
            'episode_number' => 'nullable|string|min:1|max:255',
            'overview' => 'nullable|string|min:1|max:1024',
            'status' => 'nullable|string|in:private,public',
            'thumbnail' => 'nullable|numeric|min:0|max:28800',
            'type' => 'nullable|string|in:clip,episode,movie',
            'tags' => 'nullable|array|min:0|max:15',
            'tags.*.id' => ['required', new IsExistingTag()],
        ];
    }

    public function filters(): array
    {
        return [
            'name' => 'trim|strip_tags',
            'season_number' => 'trim|empty_string_to_null|strip_tags',
            'episode_number' => 'trim|empty_string_to_null|strip_tags',
            'overview' => 'trim|empty_string_to_null|strip_tags',
            'status' => 'trim|empty_string_to_null|strip_tags|lowercase',
            'thumbnail' => 'trim|empty_string_to_null|cast:string',
            'type' => 'trim|empty_string_to_null|strip_tags|lowercase',
            'tags.*.id' => 'trim|strip_tags',
        ];
    }
}
