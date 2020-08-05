<?php

namespace App\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;
use Waavi\Sanitizer\Laravel\SanitizesInput;

class FrameshotRequest extends FormRequest
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
            'timecode' => 'required|numeric|min:0|max:28800',
        ];
    }

    /**
     *  @return array
     */
    public function filters()
    {
        return [
            'timecode' => 'trim|cast:float',
        ];
    }
}
