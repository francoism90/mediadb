<?php

namespace App\Http\Requests\User;

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
            'id' => 'required|string',
            'following' => 'nullable|boolean',
        ];
    }

    public function filters(): array
    {
        return [
            'id' => 'trim|strip_tags|cast:string',
            'following' => 'trim|strip_tags|cast:boolean',
        ];
    }
}
