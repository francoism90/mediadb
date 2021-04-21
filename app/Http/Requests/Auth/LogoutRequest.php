<?php

namespace App\Http\Requests\Auth;

use Elegant\Sanitizer\Laravel\SanitizesInput;
use Illuminate\Foundation\Http\FormRequest;

class LogoutRequest extends FormRequest
{
    use SanitizesInput;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'token' => 'required|string',
        ];
    }

    /**
     *  @return array
     */
    public function filters(): array
    {
        return [
            'token' => 'trim|strip_tags',
        ];
    }
}
