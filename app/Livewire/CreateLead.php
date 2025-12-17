<?php

namespace App\Livewire;

use App\Models\Lead;
use Livewire\Component;

class CreateLead extends Component
{
    // Personal Information Properties
    public $date;

    public $phone_number;

    public $cn_name;

    public $date_of_birth;

    public $height_weight;

    public $birth_place;

    public $medical_issue;

    public $medications;

    public $doctor_name;

    public $ssn;

    public $address;

    // New fields
    public $gender;

    public $smoker = false;

    public $beneficiary_dob;

    public $card_number;

    public $cvv;

    public $expiry_date;

    // Optional Excel fields
    public $driving_license;

    public $emergency_contact;

    public $future_draft_date;

    public $acc_number;

    public $preset_line;

    public $comments;

    // Basic Lead Properties (keeping for compatibility)
    public $coverage_amount;

    public $monthly_premium;

    public $beneficiary;

    public $policy_type;

    public $initial_draft_date;

    public $bank_name;

    public $account_type;

    public $routing_number;

    public $account_verified_by;

    public $bank_balance;

    public $source;

    public $closer_name;

    // Carrier Information Properties
    public $carrier_name;

    public $carrier_policy_number;

    public $carrier_phone;

    public $carrier_email;

    public $carrier_website;

    public $carrier_notes;

    protected $rules = [
        // Personal Information Rules
        'date' => 'required|date',
        'phone_number' => 'required|string|max:20',
        'cn_name' => 'required|string|max:255',
        'date_of_birth' => 'required|date',
        'gender' => 'nullable|in:Male,Female,Other',
        'smoker' => 'boolean',
        'ssn' => 'required|string|max:11',
        'address' => 'required|string|max:500',

        // Lead Information Rules
        'coverage_amount' => 'required|numeric|min:0',
        'monthly_premium' => 'required|numeric|min:0',
        'beneficiary' => 'required|string|max:255',
        'beneficiary_dob' => 'nullable|date|before:today',
        'policy_type' => 'required|string|max:100',
        'initial_draft_date' => 'required|date',
        'bank_name' => 'required|string|max:255',
        'account_type' => 'required|in:Checking,Savings',
        'routing_number' => 'required|string|max:20',

        // Payment card fields (encrypted)
        'card_number' => 'nullable|string|max:19',
        'cvv' => ['nullable', 'string', 'regex:/^[0-9]{3,4}$/'],
        'expiry_date' => ['nullable', 'string', 'regex:/^(0[1-9]|1[0-2])\/\d{4}$/'],

        // Optional Excel fields
        'driving_license' => 'nullable|string|max:100',
        'emergency_contact' => 'nullable|string|max:255',
        'future_draft_date' => 'nullable|date',
        'acc_number' => 'nullable|string|max:50',
        'preset_line' => 'nullable|string|max:50',
        'comments' => 'nullable|string|max:1000',

        // Carrier Rules (optional)
        'carrier_name' => 'nullable|string|max:255',
        'carrier_policy_number' => 'nullable|string|max:255',
        'carrier_phone' => 'nullable|string|max:20',
        'carrier_email' => 'nullable|email|max:255',
        'carrier_website' => 'nullable|url|max:255',
        'carrier_notes' => 'nullable|string|max:1000',
    ];

    protected $messages = [
        // Personal Information Messages
        'date.required' => 'Date is required.',
        'phone_number.required' => 'Phone number is required.',
        'cn_name.required' => 'Client name is required.',
        'date_of_birth.required' => 'Date of birth is required.',
        'ssn.required' => 'SSN is required.',
        'address.required' => 'Address is required.',

        // Lead Information Messages
        'coverage_amount.required' => 'Coverage amount is required.',
        'monthly_premium.required' => 'Monthly premium is required.',
        'beneficiary.required' => 'Beneficiary is required.',
        'policy_type.required' => 'Policy type is required.',
        'initial_draft_date.required' => 'Initial draft date is required.',
        'bank_name.required' => 'Bank name is required.',
        'account_type.required' => 'Account type is required.',
        'routing_number.required' => 'Routing number is required.',

        // Carrier Messages
        'carrier_email.email' => 'Please enter a valid email address.',
        'carrier_website.url' => 'Please enter a valid website URL.',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $validatedData = $this->validate();

        // Separate lead data from carrier data
        $leadData = collect($validatedData)->except([
            'carrier_name',
            'carrier_policy_number',
            'carrier_phone',
            'carrier_email',
            'carrier_website',
            'carrier_notes',
        ])->toArray();

        // Create the lead
        $lead = Lead::create($leadData);

        // Create carrier if carrier name is provided
        if (! empty($validatedData['carrier_name'])) {
            $lead->carriers()->create([
                'name' => $validatedData['carrier_name'],
                'policy_number' => $validatedData['carrier_policy_number'] ?? null,
                'premium_amount' => $validatedData['monthly_premium'],
                'coverage_amount' => $validatedData['coverage_amount'],
                'phone' => $validatedData['carrier_phone'] ?? null,
                'email' => $validatedData['carrier_email'] ?? null,
                'website' => $validatedData['carrier_website'] ?? null,
                'notes' => $validatedData['carrier_notes'] ?? null,
                'status' => 'pending',
            ]);
        }

        session()->flash('message', 'Lead created successfully!');

        return redirect()->route('leads.index');
    }

    public function render()
    {
        return view('livewire.create-lead');
    }
}
