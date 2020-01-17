<?php

namespace App\Http\Requests\Media;

use App\Rules\IsUniqueMedia;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'files' => 'required|array|min:1|max:2500',
            'files.*' => ['file', 'mimes:mp4,m4v,webm,ogv', 'max:9500000', new IsUniqueMedia()],
        ];
    }
}
