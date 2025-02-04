<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrganizationSignupRequest extends FormRequest
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
            'full_name' => 'required',
            'username' => 'required',
            'email' => 'required|email',
        ];
    }
    function  messages(): array
    {
        return [
            'full_name.required' => 'Please enter full_name',
            'username.required' => 'Please enter username',
            'email.required' => 'Please enter email',
        ];
    }
}
