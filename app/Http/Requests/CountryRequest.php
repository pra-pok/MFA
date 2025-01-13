<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CountryRequest extends FormRequest
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
            'name' => 'required',
            'rank' => 'required',
            'iso_code' => 'required',
            'currency' => 'required',
        ];
    }
    function  messages(): array
    {
        return [
            'name.required' => 'Please enter name',
            'rank' => 'Please enter rank',
            'iso_code.required' => 'Please enter iso code',
            'currency.required' => 'Please enter currency',
        ];
    }
}
