<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
            'username' => ['required', 'string', Rule::unique('users', 'username')->ignore(auth()->user()->id)],
            // 'password' => ['nullable'],
            'gender' => ['required', Rule::in(['L', 'P'])],
            'birth' => 'nullable',
            'last_education' => 'nullable',
            'address' => 'nullable',
            'phone' => ['nullable', Rule::unique('users', 'phone')->ignore(auth()->user()->id)],
        ];
    }
}
