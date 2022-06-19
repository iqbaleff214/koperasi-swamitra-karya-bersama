<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
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
            'nik' => ['required', 'numeric', 'digits:16', Rule::unique('customers', 'nik')],
            'name' => ['required', 'string'],
            'number' => ['required', Rule::unique('customers', 'number')],
            'gender' => ['required', Rule::in(['L', 'P'])],
            'birth' => 'required',
            'address' => 'required',
            'phone' => ['required', Rule::unique('customers', 'phone')],
            'last_education' => 'required',
            'profession' => 'required',

            'amount' => ['required', 'numeric', 'gt:0'],
        ];
    }
}
