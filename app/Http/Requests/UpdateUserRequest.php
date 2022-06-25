<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string'],
            'username' => ['required', 'string', Rule::unique('users', 'username')->ignore($this->id)],
            // 'password' => ['nullable'],
            'gender' => ['required', Rule::in(['L', 'P'])],
            'birth' => 'required',
            'last_education' => 'nullable',
            'address' => 'required',
            'phone' => ['required', Rule::unique('users', 'phone')->ignore($this->id)],
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
