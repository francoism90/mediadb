<?php

namespace App\Http\Requests\Media;

use App\Support\Sanitizer\SlugifyFilter;
use Illuminate\Foundation\Http\FormRequest;
use Waavi\Sanitizer\Laravel\SanitizesInput;

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
            'name' => 'nullable|string|min:1|max:255',
            'description' => 'nullable|string|min:0|max:1024',
            'status' => 'nullable|string|in:private,public',
            'snapshot' => 'nullable|numeric|min:0|max:14400',
            'collect' => 'nullable|array|min:0|max:25',
            'collect.*' => 'required|array',
            'collect.*.id' => 'required|string|min:1|max:255',
            'collect.*.name' => 'required|string|min:1|max:255',
            'tags' => 'nullable|array|min:0|max:15',
            'tags.*' => 'required|array',
            'tags.*.id' => 'required|string|min:1|max:255',
            'tags.*.slug' => 'required|string|min:1|max:255',
            'tags.*.type' => 'nullable|string|in:category,people,language',
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
            'snapshot' => 'trim|cast:float',
            'status' => 'trim|escape|lowercase',
            'collect.*.id' => 'trim|strip_tags',
            'collect.*.name' => 'trim|strip_tags',
            'tags.*.id' => 'trim|strip_tags',
            'tags.*.slug' => 'trim|strip_tags|slug',
            'tags.*.type' => 'trim|strip_tags|slug',
            'tags.*.name' => 'trim|strip_tags',
        ];
    }

    /**
     * @return array
     */
    public function customFilters()
    {
        return [
            'slug' => SlugifyFilter::class,
        ];
    }
}
