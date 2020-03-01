<?php

namespace App\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
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
            'name' => 'nullable|string|min:1|max:255',
            'description' => 'nullable|string|min:1|max:2048',
            'snapshot' => 'nullable|numeric|min:0|max:14400',
            'status' => 'nullable|string|in:private,public',
            'collect' => 'nullable|array',
            'collect.*' => 'required|array',
            'tags' => 'nullable|array',
            'tags.*' => 'required|array',
        ];
    }
}
