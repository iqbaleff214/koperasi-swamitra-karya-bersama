<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
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
            'nik' => ['required', 'numeric', 'digits:16', Rule::unique('customers', 'nik')->ignore($this->id)],
            'name' => ['required', 'string'],
            'number' => ['required', Rule::unique('customers', 'number')->ignore($this->id)],
            'gender' => ['required', Rule::in(['L', 'P'])],
            'birth' => 'required',
            'address' => 'required',
            'phone' => ['required', Rule::unique('customers', 'phone')->ignore($this->id)],
            'last_education' => 'required',
            'profession' => 'required',
            'status' => 'required',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
