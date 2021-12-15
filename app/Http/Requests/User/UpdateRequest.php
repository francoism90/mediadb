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
            'settings' => 'nullable|array|min:0|max:30',
            'settings.language' => 'nullable|string|in:en-us',
            'settings.captions' => 'nullable|array|min:0|max:15',
            'settings.captions.*' => 'required|array',
            'settings.captions.*.locale' => 'required|string|in:en-us',
        ];
    }

    public function filters(): array
    {
        return [
            'settings' => 'trim|empty_string_to_null',
            'settings.language' => 'trim|strip_tags|lowercase|slug',
            'settings.captions.*.locale' => 'trim|strip_tags|lowercase|slug',
        ];
    }
}
