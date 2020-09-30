<?php

namespace App\Http\Requests\Collection;

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
            'name' => 'required|string|min:1|max:255',
            'description' => 'nullable|string|min:0|max:1024',
            'status' => 'nullable|string|in:private,public',
            'tags' => 'nullable|array|min:0|max:15',
            'tags.*' => 'required|array',
            'tags.*.id' => 'required|string|min:1|max:255',
            'tags.*.type' => 'nullable|string|in:actor,genre,language,studio',
            'tags.*.name' => 'required|string|min:1|max:255',
        ];
    }

    /**
     *  @return array
     */
    public function filters()
    {
        return [
            'name' => 'trim|strip_tags',
            'description' => 'trim|strip_tags',
            'status' => 'trim|escape|lowercase',
            'tags.*.id' => 'trim|strip_tags',
            'tags.*.type' => 'trim|strip_tags|slug',
            'tags.*.name' => 'trim|strip_tags',
        ];
    }
}
