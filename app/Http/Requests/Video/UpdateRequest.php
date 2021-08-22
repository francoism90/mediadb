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
            'type' => 'nullable|string|in:clip,episode,movie',
            'tags' => 'nullable|array|min:0|max:15',
            'tags.*.id' => ['required', new IsExistingTag()],
        ];
    }

    public function filters(): array
    {
        return [
            'name' => 'trim|strip_tags',
            'season_number' => 'trim|strip_tags',
            'episode_number' => 'trim|strip_tags',
            'overview' => 'trim|strip_tags',
            'status' => 'trim|strip_tags|lowercase',
            'type' => 'trim|strip_tags|lowercase',
            'tags.*.id' => 'trim|strip_tags',
        ];
    }
}
