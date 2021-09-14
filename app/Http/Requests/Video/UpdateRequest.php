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
            'capture_time' => 'nullable|numeric|min:0|max:28800',
            'season_number' => 'nullable|string|min:1|max:255',
            'episode_number' => 'nullable|string|min:1|max:255',
            'overview' => 'nullable|string|min:1|max:1024',
            'status' => 'nullable|string|in:private,public',
            'tags' => 'nullable|array|min:0|max:15',
            'tags.*.id' => ['required', new IsExistingTag()],
            'type' => 'nullable|string|in:clip,episode,movie',
        ];
    }

    public function filters(): array
    {
        return [
            'name' => 'trim|strip_tags',
            'capture_time' => 'cast:float',
            'season_number' => 'trim|strip_tags',
            'episode_number' => 'trim|strip_tags',
            'overview' => 'trim|strip_tags',
            'status' => 'trim|strip_tags|lowercase',
            'tags.*.id' => 'trim|strip_tags',
            'type' => 'trim|strip_tags|lowercase',
        ];
    }
}
