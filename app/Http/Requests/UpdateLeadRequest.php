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
            // ── Required ──────────────────────────────────────────────
            'phone_number'            => ['required', 'string', 'max:20'],
            'cn_name'                 => ['required', 'string', 'max:255'],

            // ── Personal ──────────────────────────────────────────────
            'date'                    => ['nullable', 'date'],
            'date_of_birth'           => ['nullable', 'date'],
            'age'                     => ['nullable', 'integer', 'min:0', 'max:120'],
            'gender'                  => ['nullable', 'string', 'in:Male,Female,Other'],
            'ssn'                     => ['nullable', 'string', 'max:11'],
            'height'                  => ['nullable', 'string', 'max:50'],
            'weight'                  => ['nullable', 'string', 'max:50'],
            'birth_place'             => ['nullable', 'string', 'max:255'],
            'smoker'                  => ['nullable', 'string', 'in:yes,no'],
            'driving_license'         => ['nullable'],
            'driving_license_number'  => ['nullable', 'string', 'max:100'],
            'source'                  => ['nullable', 'string', 'max:255'],

            // ── Contact ───────────────────────────────────────────────
            'secondary_phone_number'  => ['nullable', 'string', 'max:20'],
            'address'                 => ['nullable', 'string'],
            'state'                   => ['nullable', 'string', 'max:10'],
            'zip_code'                => ['nullable', 'string', 'max:10'],
            'emergency_contact'       => ['nullable', 'string', 'max:255'],

            // ── Health ────────────────────────────────────────────────
            'medical_issue'           => ['nullable', 'string'],
            'medications'             => ['nullable', 'string'],
            'has_other_insurances'    => ['nullable'],
            'doctor_name'             => ['nullable', 'string', 'max:255'],
            'doctor_number'           => ['nullable', 'string', 'max:30'],
            'doctor_address'          => ['nullable', 'string', 'max:500'],

            // ── Insurance / Policy ────────────────────────────────────
            'carrier_name'            => ['nullable', 'string', 'max:255'],
            'policy_type'             => ['nullable', 'string', 'max:100'],
            'settlement_type'         => ['nullable', 'string', 'max:100'],
            'policy_number'           => ['nullable', 'string', 'max:100'],
            'coverage_amount'         => ['nullable', 'numeric'],
            'monthly_premium'         => ['nullable', 'numeric'],
            'initial_draft_date'      => ['nullable', 'date'],
            'future_draft_date'       => ['nullable', 'date'],
            'beneficiary'             => ['nullable', 'string', 'max:255'],
            'beneficiary_dob'         => ['nullable', 'date'],
            'beneficiaries'           => ['nullable', 'array'],
            'beneficiaries.*.name'    => ['nullable', 'string', 'max:255'],
            'beneficiaries.*.dob'     => ['nullable', 'date'],
            'beneficiaries.*.relation'=> ['nullable', 'string', 'max:100'],

            // ── Banking ───────────────────────────────────────────────
            'bank_name'               => ['nullable', 'string', 'max:255'],
            'account_type'            => ['nullable', 'string', 'max:50'],
            'account_title'           => ['nullable', 'string', 'max:255'],
            'routing_number'          => ['nullable', 'string', 'max:20'],
            'acc_number'              => ['nullable', 'string', 'max:50'],
            'bank_balance'            => ['nullable', 'numeric'],
            'account_verified_by'     => ['nullable', 'string', 'max:255'],
            'ss_amount'               => ['nullable', 'numeric'],
            'ss_date'                 => ['nullable', 'date'],
            'card_number'             => ['nullable', 'string', 'max:25'],
            'cvv'                     => ['nullable', 'string', 'max:4'],
            'expiry_date'             => ['nullable', 'string', 'max:10'],

            // ── Sale Assignment ───────────────────────────────────────
            'closer_name'             => ['nullable', 'string', 'max:255'],
            'partner_id'              => ['nullable', 'integer', 'exists:partners,id'],
            'sale_at'                 => ['nullable', 'date'],
            'sale_date'               => ['nullable', 'date'],
            'team'                    => ['nullable', 'string', 'max:100'],
            'preset_line'             => ['nullable', 'string', 'max:100'],

            // ── Status & Notes ────────────────────────────────────────
            'status'                  => ['nullable', 'string', 'in:pending,forwarded,active,cancelled,completed,accepted,rejected,underwriting,chargeback,approved,declined,unassigned'],
            'status_notes'            => ['nullable', 'string'],
            'decline_reason'          => ['nullable', 'string', 'max:500'],
            'comments'                => ['nullable', 'string'],
            'staff_notes'             => ['nullable', 'string'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Convert boolean/numeric smoker to 'yes'/'no' for ENUM
        if ($this->has('smoker')) {
            $smokerValue = $this->input('smoker');
            if (is_bool($smokerValue) || is_numeric($smokerValue)) {
                $this->merge([
                    'smoker' => $smokerValue ? 'yes' : 'no'
                ]);
            }
        }
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
