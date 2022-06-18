<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
            'username' => ['required', 'string', Rule::unique('users', 'username')],
            'password' => ['required'],
            'gender' => ['required', Rule::in(['L', 'P'])],
            'role' => ['required', Rule::in(['manager', 'teller', 'collector'])],
            'birth' => 'required',
            'last_education' => 'required',
            'joined_at' => 'required',
            'address' => 'required',
            'phone' => ['required', Rule::unique('users', 'phone')],
        ];
    }
}
