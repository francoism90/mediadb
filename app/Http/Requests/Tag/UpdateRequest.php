<?php

namespace App\Http\Requests\Tag;

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
            'type' => 'nullable|array|min:0|max:15',
            'type.id' => 'nullable|string|in:actor,genre,language,studio',
            'type.name' => 'required|string|min:1|max:255',
            'order_column' => 'nullable|int',
        ];
    }

    /**
     *  @return array
     */
    public function filters()
    {
        return [
            'name' => 'trim|strip_tags',
            'type.id' => 'trim|strip_tags|slug',
            'type.name' => 'trim|strip_tags',
            'order_column' => 'cast:int',
        ];
    }
}
