<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoanRequest extends FormRequest
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
            'customer_id' => 'required',
            'period' => ['required', 'numeric', 'gt:0'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'installment' => ['required', 'numeric', 'gt:0'],
            'return_amount' => ['required', 'numeric', 'gt:0'],

            'name' => ['required'],
            'value' => ['required', 'numeric', 'gt:amount'],
            'description' => 'nullable',
        ];
    }
}
