<?php

namespace App\Http\Requests\Video;

use Elegant\Sanitizer\Laravel\SanitizesInput;
use Illuminate\Foundation\Http\FormRequest;

class FollowRequest extends FormRequest
{
    use SanitizesInput;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'following' => 'nullable|boolean',
        ];
    }

    public function filters(): array
    {
        return [
            'following' => 'trim|empty_string_to_null|cast:boolean',
        ];
    }
}
