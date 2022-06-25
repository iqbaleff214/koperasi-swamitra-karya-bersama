<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreForeclosureRequest extends FormRequest
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
            'collateral_amount' => ['required', 'numeric', 'gte:0'],
            'remaining_amount' => ['required', 'numeric', 'gte:0'],
            'return_amount' => ['required', 'numeric', 'gte:0'],
            'customer_id' => 'required',
            'loan_id' => 'required',
            'collateral_id' => 'required',
        ];
    }
}
