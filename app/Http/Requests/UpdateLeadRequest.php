<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeadRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'phone_number' => ['required', 'string', 'max:20'],
            'cn_name' => ['required', 'string', 'max:255'],
            'date' => ['nullable', 'date'],
            'date_of_birth' => ['nullable', 'date'],
            'ssn' => ['nullable', 'string', 'max:11'],
            'address' => ['nullable', 'string'],
            'coverage_amount' => ['nullable', 'numeric'],
            'monthly_premium' => ['nullable', 'numeric'],
            'beneficiary' => ['nullable', 'string', 'max:255'],
            'policy_type' => ['nullable', 'string', 'max:100'],
            'initial_draft_date' => ['nullable', 'date'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_type' => ['nullable', 'string', 'max:50'],
            'routing_number' => ['nullable', 'string', 'max:20'],
            'height_weight' => ['nullable', 'string', 'max:100'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'medical_issue' => ['nullable', 'string'],
            'medications' => ['nullable', 'string'],
            'doctor_name' => ['nullable', 'string', 'max:255'],
            'carrier_name' => ['nullable', 'string', 'max:255'],
            'account_verified_by' => ['nullable', 'string', 'max:255'],
            'bank_balance' => ['nullable', 'numeric'],
            'source' => ['nullable', 'string', 'max:255'],
            'closer_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'phone_number.required' => 'Phone number is required.',
            'cn_name.required' => 'Customer name is required.',
            'phone_number.max' => 'Phone number must not exceed 20 characters.',
            'cn_name.max' => 'Customer name must not exceed 255 characters.',
        ];
    }
}
