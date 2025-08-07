<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|integer|min:1',
            'reference_id' => 'sometimes|nullable|string|max:255|unique:transactions,reference_id',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'Amount is required',
            'amount.integer' => 'Amount must be an integer (kopecks)',
            'amount.min' => 'Amount must be greater than zero',
            'reference_id.unique' => 'Reference ID already exists',
        ];
    }
}
