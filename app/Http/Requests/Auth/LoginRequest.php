<?php

namespace App\Http\Requests\Auth;

use Elegant\Sanitizer\Laravel\SanitizesInput;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email' => 'required|email',
            'password' => 'required|string|min:1|max:32',
            'device_name' => 'string|nullable',
            'remember_me' => 'boolean|nullable',
        ];
    }

    /**
     *  @return array
     */
    public function filters(): array
    {
        return [
            'email' => 'trim|strip_tags',
            'password' => 'trim|strip_tags',
            'device_name' => 'trim|strip_tags',
            'remember_me' => 'trim|strip_tags|cast:boolean',
        ];
    }
}
