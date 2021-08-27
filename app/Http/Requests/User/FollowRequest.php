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
            'follow' => 'nullable|boolean',
        ];
    }

    public function filters(): array
    {
        return [
            'follow' => 'trim|strip_tags|cast:boolean',
        ];
    }
}
