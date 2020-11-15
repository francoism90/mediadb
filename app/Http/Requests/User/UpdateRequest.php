<?php

namespace App\Http\Requests\User;

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
            'settings' => 'nullable|array|min:0|max:30',
            'settings.language' => 'nullable|string|in:en-us',
            'settings.captions' => 'nullable|array|min:0|max:15',
            'settings.captions.*' => 'required|array',
            'settings.captions.*.locale' => 'required|string|in:en-us',
            'settings.subtitles' => 'nullable|array|min:0|max:15',
            'settings.subtitles.*' => 'required|array',
            'settings.subtitles.*.locale' => 'required|string|in:en-us',
        ];
    }

    /**
     *  @return array
     */
    public function filters()
    {
        return [
            'settings.language' => 'trim|strip_tags|lowercase|slug',
            'settings.captions.*.locale' => 'trim|strip_tags|lowercase|slug',
            'settings.subtitles.*.locale' => 'trim|strip_tags|lowercase|slug',
        ];
    }
}
