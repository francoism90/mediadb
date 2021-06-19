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
            'id' => 'required|string',
            'favorite' => 'nullable|boolean',
        ];
    }

    public function filters(): array
    {
        return [
            'id' => 'trim|strip_tags|cast:string',
            'favorite' => 'trim|strip_tags|cast:boolean',
        ];
    }
}
