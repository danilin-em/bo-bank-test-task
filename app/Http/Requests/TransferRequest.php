<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
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
            'from_account_id' => 'required|integer|exists:accounts,id',
            'to_account_id' => 'required|integer|exists:accounts,id|different:from_account_id',
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
            'from_account_id.required' => 'Source account is required',
            'from_account_id.exists' => 'Source account not found',
            'to_account_id.required' => 'Destination account is required',
            'to_account_id.exists' => 'Destination account not found',
            'to_account_id.different' => 'Cannot transfer to the same account',
            'amount.required' => 'Amount is required',
            'amount.integer' => 'Amount must be an integer (kopecks)',
            'amount.min' => 'Amount must be greater than zero',
            'reference_id.unique' => 'Reference ID already exists',
        ];
    }
}
