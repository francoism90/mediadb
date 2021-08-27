<?php

namespace App\Http\Requests\User;

use Elegant\Sanitizer\Laravel\SanitizesInput;
use Illuminate\Foundation\Http\FormRequest;

class FavoriteRequest extends FormRequest
{
    use SanitizesInput;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'favorite' => 'nullable|boolean',
        ];
    }

    public function filters(): array
    {
        return [
            'favorite' => 'trim|strip_tags|cast:boolean',
        ];
    }
}
