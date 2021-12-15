<?php

namespace App\Http\Requests\Media;

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
            'thumbnail' => 'nullable|numeric|min:0|max:28800',
        ];
    }

    public function filters(): array
    {
        return [
            'thumbnail' => 'trim|empty_string_to_null|cast:float',
        ];
    }
}
