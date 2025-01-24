<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrganizationGalleryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
//            'gallery_category_id' => 'required',
            'media_file.*' => 'nullable',
        ];
    }
    function  messages(): array
    {
        return [
         //   'gallery_category_id.required' => 'Please select gallery_category name',
            'media_file.*' => 'mimes:jpg,jpeg,png,gif,mp4|max:102400',
        ];
    }
}
