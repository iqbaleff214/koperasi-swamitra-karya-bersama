<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLoanRequest extends FormRequest
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
            'period' => 'required',
            'amount' => 'required',
            'installment' => 'required',
            'return_amount' => 'required',

            'name' => ['required'],
            'value' => ['required', 'numeric', 'gte:amount'],
            'description' => 'nullable',
        ];
    }
}
