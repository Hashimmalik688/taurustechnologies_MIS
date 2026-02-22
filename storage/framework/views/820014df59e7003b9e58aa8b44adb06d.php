<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Details - <?php echo e($lead->cn_name); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Calibri, Arial, sans-serif;
            background-color: var(--bs-surface-50);
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
            color: var(--bs-surface-700);
            white-space: pre-wrap;
            word-wrap: break-word;
            font-family: Calibri, Arial, sans-serif;
        }
        
        .print-button {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .btn {
            background-color: var(--bs-primary);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: var(--bs-ui-info-dark);
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
    
    <div class="container">Carrier Name: <?php echo e($lead->carrier_name ?? '_________________'); ?>

Plan Type: <?php echo e($lead->policy_type ?? '_________________'); ?>

Policy Number: <?php echo e($lead->policy_number ?? '_________________'); ?>

Coverage Amount: <?php echo e($lead->coverage_amount ? '$' . number_format($lead->coverage_amount, 2) : '_________________'); ?>

Monthly Premium: <?php echo e($lead->monthly_premium ? '$' . number_format($lead->monthly_premium, 2) : '_________________'); ?>

First Coverage: <?php echo e($lead->initial_draft_date ? \Carbon\Carbon::parse($lead->initial_draft_date)->format('M d, Y') : '_________________'); ?>

Additional Coverage: <?php echo e($lead->future_draft_date ? \Carbon\Carbon::parse($lead->future_draft_date)->format('M d, Y') : '_________________'); ?>


Phone Number: <?php echo e($lead->phone_number ?? '_________________'); ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->secondary_phone_number): ?>
Secondary Phone: <?php echo e($lead->secondary_phone_number); ?>

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
Name: <?php echo e($lead->cn_name ?? '_________________'); ?>


Date of Birth: <?php echo e($lead->date_of_birth ? \Carbon\Carbon::parse($lead->date_of_birth)->format('m/d/Y') : '_________________'); ?>

Address: <?php echo e($lead->address ?? '_________________'); ?>

State: <?php echo e($lead->state ?? '_________________'); ?>

Zip Code: <?php echo e($lead->zip_code ?? '_________________'); ?>

Gender: <?php echo e($lead->gender ?? '_________________'); ?>

Height: <?php echo e($lead->height ?? '_________________'); ?>

Weight: <?php echo e($lead->weight ? $lead->weight . ' lbs' : '_________________'); ?>

Nicotine User: <?php echo e($lead->smoker ? 'Yes' : 'No'); ?>

Birth Place: <?php echo e($lead->birth_place ?? '_________________'); ?>

Emergency Contact: <?php echo e($lead->emergency_contact ?? '_________________'); ?>

Driving License: <?php echo e($lead->driving_license !== null ? ($lead->driving_license ? 'Yes' : 'No') : '_________________'); ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->driving_license_number): ?>
DL Number: <?php echo e($lead->driving_license_number); ?>

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

Primary Care Physician: <?php echo e($lead->doctor_name ?? '_________________'); ?>

Doctor Phone: <?php echo e($lead->doctor_number ?? '_________________'); ?>

Doctor Address: <?php echo e($lead->doctor_address ?? '_________________'); ?>

Medical Issues: <?php echo e($lead->medical_issue ?? '_________________'); ?>

Medications: <?php echo e($lead->medications ?? '_________________'); ?>


SSN: <?php echo e($lead->ssn ?? '_________________'); ?>


<?php
    $beneficiaries = $lead->beneficiaries ?? [];
    if (is_string($beneficiaries)) {
        $decoded = json_decode($beneficiaries, true);
        $beneficiaries = is_array($decoded) ? $decoded : [];
    }
    if (!is_array($beneficiaries)) {
        $beneficiaries = [];
    }
    if (empty($beneficiaries) && ($lead->beneficiary || $lead->beneficiary_dob)) {
        $beneficiaries = [[
            'name' => $lead->beneficiary ?? '',
            'dob' => $lead->beneficiary_dob ?? '',
            'relation' => ''
        ]];
    }
?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($beneficiaries)): ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $beneficiaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $beneficiary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
Benif. <?php echo e($index + 1); ?>: <?php echo e($beneficiary['name'] ?? '_________________'); ?> | <?php echo e(!empty($beneficiary['dob']) ? \Carbon\Carbon::parse($beneficiary['dob'])->format('m/d/Y') : '___/___/____'); ?> | <?php echo e($beneficiary['relation'] ?? '_________________'); ?>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php else: ?>
Benif. 1: _________________ | ___/___/____ | _________________
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
Name of the Bank: <?php echo e($lead->bank_name ?? '_________________'); ?>

Account Title: <?php echo e($lead->account_title ?? '_________________'); ?>

Account Type: <?php echo e($lead->account_type ?? '_________________'); ?>

Routing Number: <?php echo e($lead->routing_number ?? '_________________'); ?>

Account Number: <?php echo e($lead->acc_number ?? $lead->account_number ?? '_________________'); ?>

Bank Balance: <?php echo e($lead->bank_balance ? '$' . number_format($lead->bank_balance, 2) : '_________________'); ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->ss_amount || $lead->ss_date): ?>
SS Amount: <?php echo e($lead->ss_amount ? '$' . number_format($lead->ss_amount, 2) : '_________________'); ?>

SS Date: <?php echo e($lead->ss_date ? \Carbon\Carbon::parse($lead->ss_date)->format('M d, Y') : '_________________'); ?>

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->card_number || $lead->cvv || $lead->expiry_date): ?>
Card Number: <?php echo e($lead->card_number ?? '_________________'); ?>

CVV: <?php echo e($lead->cvv ?? '_________________'); ?>

Expiry Date: <?php echo e($lead->expiry_date ?? '_________________'); ?>


<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
First Draft Date: <?php echo e($lead->initial_draft_date ? \Carbon\Carbon::parse($lead->initial_draft_date)->format('M d, Y') : '_________________'); ?>

Future Draft Date: <?php echo e($lead->future_draft_date ? \Carbon\Carbon::parse($lead->future_draft_date)->format('d') : '___'); ?> of Next Month

Lead Source: <?php echo e($lead->source ?? '_________________'); ?>

Team: <?php echo e(ucfirst($lead->team ?? '_________________')); ?>

Closer Name: <?php echo e($lead->closer_name ?? '_________________'); ?>

Verified By: <?php echo e($lead->account_verified_by ?? '_________________'); ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->verifier): ?>
Verifier: <?php echo e($lead->verifier->name ?? '_________________'); ?>

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->validator): ?>
Validator: <?php echo e($lead->validator->name ?? '_________________'); ?>

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
Sale Date: <?php echo e($lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : '_________________'); ?>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->comments): ?>
Comments: <?php echo e($lead->comments); ?>


<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->staff_notes): ?>
Staff Notes: <?php echo e($lead->staff_notes); ?>


<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
Generated: <?php echo e(now()->format('F j, Y \a\t g:i A')); ?></div>
</body>
</html>
<?php /**PATH /var/www/taurus-crm/resources/views/admin/sales/pretty-print.blade.php ENDPATH**/ ?>