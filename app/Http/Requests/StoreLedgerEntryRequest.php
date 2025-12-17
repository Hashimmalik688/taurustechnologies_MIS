<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLedgerEntryRequest extends FormRequest
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
            'vendor_id' => ['required', 'exists:vendors,id'],
            'lead_id' => ['nullable', 'exists:leads,id'],
            'transaction_date' => ['required', 'date'],
            'type' => ['required', 'in:debit,credit'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'vendor_id.required' => 'Please select a vendor.',
            'vendor_id.exists' => 'The selected vendor does not exist.',
            'lead_id.exists' => 'The selected lead does not exist.',
            'transaction_date.required' => 'Transaction date is required.',
            'transaction_date.date' => 'Please provide a valid date.',
            'type.required' => 'Transaction type is required.',
            'type.in' => 'Transaction type must be either debit or credit.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a valid number.',
            'amount.min' => 'Amount must be at least 0.01.',
            'description.required' => 'Description is required.',
        ];
    }
}
