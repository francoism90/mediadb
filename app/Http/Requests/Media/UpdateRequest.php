<?php

namespace App\Http\Requests\Media;

use Elegant\Sanitizer\Laravel\SanitizesInput;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'thumbnail' => 'nullable|numeric|min:0|max:28800',
        ];
    }

    /**
     *  @return array
     */
    public function filters(): array
    {
        return [
            'thumbnail' => 'cast:float',
        ];
    }
}
