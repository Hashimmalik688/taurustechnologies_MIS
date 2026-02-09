<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Details - {{ $lead->cn_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Calibri, Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            line-height: 1.6;
            font-size: 12px;
            color: #333;
            white-space: pre-wrap;
            word-wrap: break-word;
            font-family: Calibri, Arial, sans-serif;
        }
        
        .print-button {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #0056b3;
        }
        
        @media print {
            body {
                background-color: white;
                padding: 0;
            }
            
            .container {
                box-shadow: none;
                padding: 20px;
                max-width: 100%;
            }
            
            .print-button {
                display: none;
            }
            
            .btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-button">
        <button class="btn" onclick="window.print()">🖨️ Print / Save as PDF</button>
    </div>
    
    <div class="container">Carrier Name: {{ $lead->carrier_name ?? '_________________' }}
Plan Type: {{ $lead->policy_type ?? '_________________' }}
Coverage Amount: {{ $lead->coverage_amount ? '$' . number_format($lead->coverage_amount, 2) : '_________________' }}
Monthly Premium: {{ $lead->monthly_premium ? '$' . number_format($lead->monthly_premium, 2) : '_________________' }}
First Coverage: {{ $lead->initial_draft_date ? \Carbon\Carbon::parse($lead->initial_draft_date)->format('M d, Y') : '_________________' }}
Additional Coverage: {{ $lead->future_draft_date ? \Carbon\Carbon::parse($lead->future_draft_date)->format('M d, Y') : '_________________' }}

Phone Number: {{ $lead->phone_number ?? '_________________' }}
@if($lead->secondary_phone_number)
Secondary Phone: {{ $lead->secondary_phone_number }}
@endif
Name: {{ $lead->cn_name ?? '_________________' }}

Date of Birth: {{ $lead->date_of_birth ? \Carbon\Carbon::parse($lead->date_of_birth)->format('m/d/Y') : '_________________' }}
Address: {{ $lead->address ?? '_________________' }}
State: {{ $lead->state ?? '_________________' }}
Zip Code: {{ $lead->zip_code ?? '_________________' }}
Gender: {{ $lead->gender ?? '_________________' }}
Height & Weight: {{ $lead->height_weight ?? '_________________' }}
Nicotine User: {{ $lead->smoker ? 'Yes' : 'No' }}
Birth Place: {{ $lead->birth_place ?? '_________________' }}

Primary Care Physician: {{ $lead->doctor_name ?? '_________________' }}
Doctor Phone: {{ $lead->doctor_number ?? '_________________' }}
Doctor Address: {{ $lead->doctor_address ?? '_________________' }}
Medical Issues: {{ $lead->medical_issue ?? '_________________' }}
Medications: {{ $lead->medications ?? '_________________' }}

SSN: {{ $lead->ssn ?? '_________________' }}

@if(!empty($lead->beneficiaries))
@foreach($lead->beneficiaries as $index => $beneficiary)
Benif. {{ $index + 1 }}: {{ $beneficiary['name'] ?? '_________________' }} | {{ !empty($beneficiary['dob']) ? \Carbon\Carbon::parse($beneficiary['dob'])->format('m/d/Y') : '___/___/____' }} | {{ $beneficiary['relation'] ?? '_________________' }}
@endforeach
@else
Benif. 1: {{ $lead->beneficiary ?? '_________________' }} | {{ $lead->beneficiary_dob ? \Carbon\Carbon::parse($lead->beneficiary_dob)->format('m/d/Y') : '___/___/____' }} | _________________
@endif
Name of the Bank: {{ $lead->bank_name ?? '_________________' }}
Account Type: {{ $lead->account_type ?? '_________________' }}
Routing Number: {{ $lead->routing_number ?? '_________________' }}
Account Number: {{ $lead->account_number ?? '_________________' }}
Bank Balance: {{ $lead->bank_balance ? '$' . number_format($lead->bank_balance, 2) : '_________________' }}

@if($lead->card_number || $lead->cvv || $lead->expiry_date)
Card Number: {{ $lead->card_number ?? '_________________' }}
CVV: {{ $lead->cvv ?? '_________________' }}
Expiry Date: {{ $lead->expiry_date ?? '_________________' }}

@endif
First Draft Date: {{ $lead->initial_draft_date ? \Carbon\Carbon::parse($lead->initial_draft_date)->format('M d, Y') : '_________________' }}
Future Draft Date: {{ $lead->future_draft_date ? \Carbon\Carbon::parse($lead->future_draft_date)->format('d') : '___' }} of Next Month

Lead Source: {{ $lead->source ?? '_________________' }}
Team: {{ ucfirst($lead->team ?? '_________________') }}
Closer Name: {{ $lead->closer_name ?? '_________________' }}
Verified By: {{ $lead->account_verified_by ?? '_________________' }}
@if($lead->verifier)
Verifier: {{ $lead->verifier->name ?? '_________________' }}
@endif
@if($lead->validator)
Validator: {{ $lead->validator->name ?? '_________________' }}
@endif
Sale Date: {{ $lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : '_________________' }}

@if($lead->comments)
Comments: {{ $lead->comments }}

@endif
@if($lead->staff_notes)
Staff Notes: {{ $lead->staff_notes }}

@endif
Generated: {{ now()->format('F j, Y \a\t g:i A') }}</div>
</body>
</html>
