<?php

namespace App\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;
use Waavi\Sanitizer\Laravel\SanitizesInput;

class SaveRequest extends FormRequest
{
    use SanitizesInput;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'collections' => 'nullable|array|min:0|max:25',
            'collections.*' => 'required|array',
            'collections.*.id' => 'required|string|min:1|max:255',
            'collections.*.name' => 'required|string|min:1|max:255',
        ];
    }

    /**
     *  @return array
     */
    public function filters()
    {
        return [
            'collections.*.id' => 'trim|strip_tags',
            'collections.*.name' => 'trim|strip_tags',
        ];
    }
}
