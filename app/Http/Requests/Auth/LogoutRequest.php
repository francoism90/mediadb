<?php

namespace App\Http\Requests\Auth;

use Elegant\Sanitizer\Laravel\SanitizesInput;
use Illuminate\Foundation\Http\FormRequest;

class LogoutRequest extends FormRequest
{
    use SanitizesInput;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => 'required|string',
        ];
    }

    public function filters(): array
    {
        return [
            'token' => 'trim|strip_tags',
        ];
    }
}
