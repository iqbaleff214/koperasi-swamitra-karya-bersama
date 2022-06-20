<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDepositRequest extends FormRequest
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
            'created_at' => 'nullable',
            'amount' => ['required', 'numeric', 'gt:0'],
            'type' => ['required'],
            'customer_id' => 'required',
            'loan_id' => ['nullable', 'required_if:type,wajib'],
        ];
    }
}
