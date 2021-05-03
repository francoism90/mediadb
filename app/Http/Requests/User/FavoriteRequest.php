<?php

namespace App\Http\Requests\User;

use Elegant\Sanitizer\Laravel\SanitizesInput;
use Illuminate\Foundation\Http\FormRequest;

class FavoriteRequest extends FormRequest
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
            'id' => 'required|string',
            'favorite' => 'nullable|boolean',
        ];
    }

    /**
     *  @return array
     */
    public function filters(): array
    {
        return [
            'id' => 'trim|strip_tags|cast:string',
            'favorite' => 'trim|strip_tags|cast:boolean',
        ];
    }
}
