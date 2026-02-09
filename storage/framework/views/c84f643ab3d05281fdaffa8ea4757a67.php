<style>
    .readonly-value {
        background-color: #f8f9fa;
        padding: 10px 14px;
        border-radius: 6px;
        border: 1px solid #e0e0e0;
        font-weight: 500;
    }
    .form-label {
        font-weight: 500;
        color: #2d2d2d;
        font-size: 0.875rem;
        margin-bottom: 6px;
    }
    .form-label.required:after {
        content: '*';
        color: #dc3545;
        margin-left: 4px;
    }
    .form-section-title {
        color: #1a1a1a;
        font-size: 0.95rem;
        font-weight: 600;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 2px solid #d4af37;
    }
</style>

<!-- Verifier Information (Read-Only) -->
<div class="alert alert-info">
    <i class="fas fa-info-circle me-2"></i>
    <strong>Verified Information:</strong> The following was collected by the verifier
</div>

<div class="row g-3 mb-4">
    <div class="col-md-2">
        <label class="form-label">Date</label>
        <div class="readonly-value"><?php echo e($lead->date ?? 'N/A'); ?></div>
    </div>
    <div class="col-md-3">
        <label class="form-label required">Customer Name</label>
        <input type="text" class="form-control" name="cn_name" value="<?php echo e(old('cn_name', $lead->cn_name ?? '')); ?>" required>
    </div>
    <div class="col-md-2">
        <label class="form-label required">Phone Number</label>
        <input type="text" class="form-control" name="phone_number" value="<?php echo e(old('phone_number', $lead->phone_number ?? '')); ?>" required>
    </div>
    <div class="col-md-2">
        <label class="form-label">Verified Gender</label>
        <div class="readonly-value"><?php echo e($lead->gender ?? 'N/A'); ?></div>
    </div>
    <div class="col-md-2">
        <label class="form-label">Verified By</label>
        <div class="readonly-value"><?php echo e($lead->account_verified_by ?? 'N/A'); ?></div>
    </div>
    <div class="col-md-1">
        <label class="form-label">Closer</label>
        <div class="readonly-value"><?php echo e($lead->closer_name ?? 'N/A'); ?></div>
    </div>
</div>

<h6 class="form-section-title"><i class="bx bx-user me-2"></i>Personal Information</h6>
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label for="date_of_birth" class="form-label required">Date of Birth</label>
        <input type="date" class="form-control" name="date_of_birth" value="<?php echo e(old('date_of_birth', $lead->date_of_birth ? \Carbon\Carbon::parse($lead->date_of_birth)->format('Y-m-d') : '')); ?>" required>
    </div>
    <div class="col-md-4">
        <label for="gender" class="form-label required">Gender</label>
        <select class="form-select" name="gender" required>
            <option value="">Select Gender</option>
            <option value="Male" <?php echo e(strtoupper(old('gender', $lead->gender ?? '')) == 'MALE' ? 'selected' : ''); ?>>Male</option>
            <option value="Female" <?php echo e(strtoupper(old('gender', $lead->gender ?? '')) == 'FEMALE' ? 'selected' : ''); ?>>Female</option>
            <option value="Other" <?php echo e(strtoupper(old('gender', $lead->gender ?? '')) == 'OTHER' ? 'selected' : ''); ?>>Other</option>
        </select>
    </div>
    <div class="col-md-4">
        <label for="ssn" class="form-label required">SSN</label>
        <input type="text" class="form-control" name="ssn" value="<?php echo e(old('ssn', $lead->ssn ?? '')); ?>" placeholder="XXX-XX-XXXX" required>
    </div>
    <div class="col-md-6">
        <label for="address" class="form-label required">Full Address</label>
        <textarea class="form-control" name="address" rows="2" placeholder="Street address" required><?php echo e(old('address', $lead->address ?? '')); ?></textarea>
    </div>
    <div class="col-md-2">
        <label for="state" class="form-label required">State</label>
        <select class="form-select" name="state" id="state_select" required>
            <option value="">Select State</option>
            <option value="AL" <?php echo e(old('state', $lead->state ?? '') == 'AL' ? 'selected' : ''); ?>>Alabama (AL)</option>
            <option value="AK" <?php echo e(old('state', $lead->state ?? '') == 'AK' ? 'selected' : ''); ?>>Alaska (AK)</option>
            <option value="AZ" <?php echo e(old('state', $lead->state ?? '') == 'AZ' ? 'selected' : ''); ?>>Arizona (AZ)</option>
            <option value="AR" <?php echo e(old('state', $lead->state ?? '') == 'AR' ? 'selected' : ''); ?>>Arkansas (AR)</option>
            <option value="CA" <?php echo e(old('state', $lead->state ?? '') == 'CA' ? 'selected' : ''); ?>>California (CA)</option>
            <option value="CO" <?php echo e(old('state', $lead->state ?? '') == 'CO' ? 'selected' : ''); ?>>Colorado (CO)</option>
            <option value="CT" <?php echo e(old('state', $lead->state ?? '') == 'CT' ? 'selected' : ''); ?>>Connecticut (CT)</option>
            <option value="DE" <?php echo e(old('state', $lead->state ?? '') == 'DE' ? 'selected' : ''); ?>>Delaware (DE)</option>
            <option value="FL" <?php echo e(old('state', $lead->state ?? '') == 'FL' ? 'selected' : ''); ?>>Florida (FL)</option>
            <option value="GA" <?php echo e(old('state', $lead->state ?? '') == 'GA' ? 'selected' : ''); ?>>Georgia (GA)</option>
            <option value="HI" <?php echo e(old('state', $lead->state ?? '') == 'HI' ? 'selected' : ''); ?>>Hawaii (HI)</option>
            <option value="ID" <?php echo e(old('state', $lead->state ?? '') == 'ID' ? 'selected' : ''); ?>>Idaho (ID)</option>
            <option value="IL" <?php echo e(old('state', $lead->state ?? '') == 'IL' ? 'selected' : ''); ?>>Illinois (IL)</option>
            <option value="IN" <?php echo e(old('state', $lead->state ?? '') == 'IN' ? 'selected' : ''); ?>>Indiana (IN)</option>
            <option value="IA" <?php echo e(old('state', $lead->state ?? '') == 'IA' ? 'selected' : ''); ?>>Iowa (IA)</option>
            <option value="KS" <?php echo e(old('state', $lead->state ?? '') == 'KS' ? 'selected' : ''); ?>>Kansas (KS)</option>
            <option value="KY" <?php echo e(old('state', $lead->state ?? '') == 'KY' ? 'selected' : ''); ?>>Kentucky (KY)</option>
            <option value="LA" <?php echo e(old('state', $lead->state ?? '') == 'LA' ? 'selected' : ''); ?>>Louisiana (LA)</option>
            <option value="ME" <?php echo e(old('state', $lead->state ?? '') == 'ME' ? 'selected' : ''); ?>>Maine (ME)</option>
            <option value="MD" <?php echo e(old('state', $lead->state ?? '') == 'MD' ? 'selected' : ''); ?>>Maryland (MD)</option>
            <option value="MA" <?php echo e(old('state', $lead->state ?? '') == 'MA' ? 'selected' : ''); ?>>Massachusetts (MA)</option>
            <option value="MI" <?php echo e(old('state', $lead->state ?? '') == 'MI' ? 'selected' : ''); ?>>Michigan (MI)</option>
            <option value="MN" <?php echo e(old('state', $lead->state ?? '') == 'MN' ? 'selected' : ''); ?>>Minnesota (MN)</option>
            <option value="MS" <?php echo e(old('state', $lead->state ?? '') == 'MS' ? 'selected' : ''); ?>>Mississippi (MS)</option>
            <option value="MO" <?php echo e(old('state', $lead->state ?? '') == 'MO' ? 'selected' : ''); ?>>Missouri (MO)</option>
            <option value="MT" <?php echo e(old('state', $lead->state ?? '') == 'MT' ? 'selected' : ''); ?>>Montana (MT)</option>
            <option value="NE" <?php echo e(old('state', $lead->state ?? '') == 'NE' ? 'selected' : ''); ?>>Nebraska (NE)</option>
            <option value="NV" <?php echo e(old('state', $lead->state ?? '') == 'NV' ? 'selected' : ''); ?>>Nevada (NV)</option>
            <option value="NH" <?php echo e(old('state', $lead->state ?? '') == 'NH' ? 'selected' : ''); ?>>New Hampshire (NH)</option>
            <option value="NJ" <?php echo e(old('state', $lead->state ?? '') == 'NJ' ? 'selected' : ''); ?>>New Jersey (NJ)</option>
            <option value="NM" <?php echo e(old('state', $lead->state ?? '') == 'NM' ? 'selected' : ''); ?>>New Mexico (NM)</option>
            <option value="NY" <?php echo e(old('state', $lead->state ?? '') == 'NY' ? 'selected' : ''); ?>>New York (NY)</option>
            <option value="NC" <?php echo e(old('state', $lead->state ?? '') == 'NC' ? 'selected' : ''); ?>>North Carolina (NC)</option>
            <option value="ND" <?php echo e(old('state', $lead->state ?? '') == 'ND' ? 'selected' : ''); ?>>North Dakota (ND)</option>
            <option value="OH" <?php echo e(old('state', $lead->state ?? '') == 'OH' ? 'selected' : ''); ?>>Ohio (OH)</option>
            <option value="OK" <?php echo e(old('state', $lead->state ?? '') == 'OK' ? 'selected' : ''); ?>>Oklahoma (OK)</option>
            <option value="OR" <?php echo e(old('state', $lead->state ?? '') == 'OR' ? 'selected' : ''); ?>>Oregon (OR)</option>
            <option value="PA" <?php echo e(old('state', $lead->state ?? '') == 'PA' ? 'selected' : ''); ?>>Pennsylvania (PA)</option>
            <option value="RI" <?php echo e(old('state', $lead->state ?? '') == 'RI' ? 'selected' : ''); ?>>Rhode Island (RI)</option>
            <option value="SC" <?php echo e(old('state', $lead->state ?? '') == 'SC' ? 'selected' : ''); ?>>South Carolina (SC)</option>
            <option value="SD" <?php echo e(old('state', $lead->state ?? '') == 'SD' ? 'selected' : ''); ?>>South Dakota (SD)</option>
            <option value="TN" <?php echo e(old('state', $lead->state ?? '') == 'TN' ? 'selected' : ''); ?>>Tennessee (TN)</option>
            <option value="TX" <?php echo e(old('state', $lead->state ?? '') == 'TX' ? 'selected' : ''); ?>>Texas (TX)</option>
            <option value="UT" <?php echo e(old('state', $lead->state ?? '') == 'UT' ? 'selected' : ''); ?>>Utah (UT)</option>
            <option value="VT" <?php echo e(old('state', $lead->state ?? '') == 'VT' ? 'selected' : ''); ?>>Vermont (VT)</option>
            <option value="VA" <?php echo e(old('state', $lead->state ?? '') == 'VA' ? 'selected' : ''); ?>>Virginia (VA)</option>
            <option value="WA" <?php echo e(old('state', $lead->state ?? '') == 'WA' ? 'selected' : ''); ?>>Washington (WA)</option>
            <option value="WV" <?php echo e(old('state', $lead->state ?? '') == 'WV' ? 'selected' : ''); ?>>West Virginia (WV)</option>
            <option value="WI" <?php echo e(old('state', $lead->state ?? '') == 'WI' ? 'selected' : ''); ?>>Wisconsin (WI)</option>
            <option value="WY" <?php echo e(old('state', $lead->state ?? '') == 'WY' ? 'selected' : ''); ?>>Wyoming (WY)</option>
        </select>
    </div>
    <div class="col-md-2">
        <label for="zip_code" class="form-label required">Zip Code</label>
        <input type="text" class="form-control" name="zip_code" value="<?php echo e(old('zip_code', $lead->zip_code ?? '')); ?>" placeholder="Zip" maxlength="10" required>
    </div>
    <div class="col-md-2">
        <label for="birth_place" class="form-label">Birth Place</label>
        <input type="text" class="form-control" name="birth_place" value="<?php echo e(old('birth_place', $lead->birth_place ?? '')); ?>" placeholder="City, State">
    </div>
</div>

<h6 class="form-section-title"><i class="bx bx-heart me-2"></i>Health Information</h6>
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <label for="height_weight" class="form-label">Height & Weight</label>
        <input type="text" class="form-control" name="height_weight" value="<?php echo e(old('height_weight', $lead->height_weight ?? '')); ?>" placeholder="5'10&quot;, 180 lbs">
    </div>
    <div class="col-md-2">
        <label for="smoker" class="form-label">Smoker</label>
        <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" name="smoker" value="1" <?php echo e(old('smoker', $lead->smoker) ? 'checked' : ''); ?>>
            <label class="form-check-label">Yes</label>
        </div>
    </div>
    <div class="col-md-4">
        <label for="doctor_name" class="form-label required">Doctor Name</label>
        <input type="text" class="form-control" name="doctor_name" value="<?php echo e(old('doctor_name', $lead->doctor_name ?? '')); ?>" placeholder="Dr. Name" required>
    </div>
    <div class="col-md-3">
        <label for="doctor_number" class="form-label required">Doctor Number</label>
        <input type="text" class="form-control" name="doctor_number" value="<?php echo e(old('doctor_number', $lead->doctor_number ?? '')); ?>" placeholder="Phone number" required>
    </div>
    <div class="col-md-5">
        <label for="doctor_address" class="form-label required">Doctor Address</label>
        <input type="text" class="form-control" name="doctor_address" value="<?php echo e(old('doctor_address', $lead->doctor_address ?? '')); ?>" placeholder="Address" required>
    </div>
    <div class="col-md-6">
        <label for="medical_issue" class="form-label required">Medical Conditions</label>
        <textarea class="form-control" name="medical_issue" rows="2" placeholder="Any conditions" required><?php echo e(old('medical_issue', $lead->medical_issue ?? '')); ?></textarea>
    </div>
    <div class="col-md-6">
        <label for="medications" class="form-label required">Medications</label>
        <textarea class="form-control" name="medications" rows="2" placeholder="Current medications" required><?php echo e(old('medications', $lead->medications ?? '')); ?></textarea>
    </div>
</div>

<h6 class="form-section-title"><i class="bx bx-shield me-2"></i>Insurance Information</h6>
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label for="carrier_name" class="form-label">Carrier Name</label>
        <input type="text" class="form-control" name="carrier_name" value="<?php echo e(old('carrier_name', $lead->carrier_name ?? '')); ?>" placeholder="Insurance company">
    </div>
    <div class="col-md-4">
        <label for="policy_type" class="form-label required">Policy Type</label>
        <select class="form-select" name="policy_type" required>
            <option value="">Select Policy Type</option>
            <option value="Level" <?php echo e(old('policy_type', $lead->policy_type ?? '') == 'Level' ? 'selected' : ''); ?>>Level</option>
            <option value="Graded" <?php echo e(old('policy_type', $lead->policy_type ?? '') == 'Graded' ? 'selected' : ''); ?>>Graded</option>
            <option value="G.I" <?php echo e(old('policy_type', $lead->policy_type ?? '') == 'G.I' ? 'selected' : ''); ?>>G.I</option>
            <option value="Modified" <?php echo e(old('policy_type', $lead->policy_type ?? '') == 'Modified' ? 'selected' : ''); ?>>Modified</option>
        </select>
    </div>
    <div class="col-md-4">
        <label for="initial_draft_date" class="form-label required">Draft Date</label>
        <input type="date" class="form-control" name="initial_draft_date" value="<?php echo e(old('initial_draft_date', $lead->initial_draft_date ? \Carbon\Carbon::parse($lead->initial_draft_date)->format('Y-m-d') : '')); ?>" required>
    </div>
    <div class="col-md-4">
        <label for="coverage_amount" class="form-label required">Coverage Amount</label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="number" step="1" class="form-control" name="coverage_amount" value="<?php echo e(old('coverage_amount', $lead->coverage_amount ?? '')); ?>" placeholder="Enter coverage amount" required>
        </div>
    </div>
    <div class="col-md-4">
        <label for="monthly_premium" class="form-label required">Monthly Premium</label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="number" step="0.01" class="form-control" name="monthly_premium" value="<?php echo e(old('monthly_premium', $lead->monthly_premium ?? '')); ?>" placeholder="125.50" required>
        </div>
    </div>
    <div class="col-md-4">
        <label for="source" class="form-label">Lead Source</label>
        <input type="text" class="form-control" name="source" value="<?php echo e(old('source', $lead->source ?? '')); ?>" placeholder="Referral, Web, etc.">
    </div>
</div>

<!-- Multiple Beneficiaries Section -->
<h6 class="form-section-title"><i class="bx bx-heart me-2"></i>Beneficiary Information</h6>
<div id="beneficiaries-container" class="mb-3">
    <?php
        // Check for old input first (from failed submission), then existing beneficiaries
        $existingBeneficiaries = old('beneficiaries', $lead->beneficiaries ?? []);
        // If no beneficiaries in JSON but old fields exist, migrate them
        if (empty($existingBeneficiaries) && ($lead->beneficiary || $lead->beneficiary_dob)) {
            $existingBeneficiaries = [[
                'name' => $lead->beneficiary ?? '',
                'dob' => $lead->beneficiary_dob ?? '',
                'relation' => ''
            ]];
        }
        // Ensure at least one beneficiary row
        if (empty($existingBeneficiaries)) {
            $existingBeneficiaries = [['name' => '', 'dob' => '', 'relation' => '']];
        }
    ?>
    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $existingBeneficiaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $beneficiary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="row g-3 mb-2 beneficiary-row" data-index="<?php echo e($index); ?>">
        <div class="col-md-4">
            <label for="beneficiaries[<?php echo e($index); ?>][name]" class="form-label required">
                Beneficiary Name <?php echo e($index > 0 ? ($index + 1) : ''); ?>

            </label>
            <input type="text" class="form-control" name="beneficiaries[<?php echo e($index); ?>][name]" 
                   value="<?php echo e($beneficiary['name'] ?? ''); ?>" placeholder="Full name" required>
        </div>
        <div class="col-md-3">
            <label for="beneficiaries[<?php echo e($index); ?>][dob]" class="form-label required">Date of Birth</label>
            <input type="date" class="form-control" name="beneficiaries[<?php echo e($index); ?>][dob]" 
                   value="<?php echo e($beneficiary['dob'] ?? ''); ?>" required>
        </div>
        <div class="col-md-3">
            <label for="beneficiaries[<?php echo e($index); ?>][relation]" class="form-label">Relation</label>
            <select class="form-select" name="beneficiaries[<?php echo e($index); ?>][relation]">
                <option value="">Select relation</option>
                <option value="Spouse" <?php echo e(($beneficiary['relation'] ?? '') == 'Spouse' ? 'selected' : ''); ?>>Spouse</option>
                <option value="Child" <?php echo e(($beneficiary['relation'] ?? '') == 'Child' ? 'selected' : ''); ?>>Child</option>
                <option value="Parent" <?php echo e(($beneficiary['relation'] ?? '') == 'Parent' ? 'selected' : ''); ?>>Parent</option>
                <option value="Sibling" <?php echo e(($beneficiary['relation'] ?? '') == 'Sibling' ? 'selected' : ''); ?>>Sibling</option>
                <option value="Grandchild" <?php echo e(($beneficiary['relation'] ?? '') == 'Grandchild' ? 'selected' : ''); ?>>Grandchild</option>
                <option value="Other" <?php echo e(($beneficiary['relation'] ?? '') == 'Other' ? 'selected' : ''); ?>>Other</option>
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($index === 0): ?>
                <button type="button" class="btn btn-success w-100" id="add-beneficiary" title="Add Another Beneficiary">
                    <i class="bx bx-plus"></i> Add
                </button>
            <?php else: ?>
                <button type="button" class="btn btn-danger w-100 remove-beneficiary" title="Remove Beneficiary">
                    <i class="bx bx-minus"></i> Remove
                </button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<script>
(function() {
    let beneficiaryIndex = <?php echo e(count($existingBeneficiaries)); ?>;
    let addButtonInitialized = false;
    
    // Add beneficiary - use once() to ensure single execution per click
    function initAddButton() {
        if (addButtonInitialized) return;
        addButtonInitialized = true;
        
        const addBtn = document.getElementById('add-beneficiary');
        if (!addBtn) return;
        
        addBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            
            const container = document.getElementById('beneficiaries-container');
            const newRow = document.createElement('div');
            newRow.className = 'row g-3 mb-2 beneficiary-row';
            newRow.setAttribute('data-index', beneficiaryIndex);
            newRow.innerHTML = `<div class="col-md-4"><label for="beneficiaries[${beneficiaryIndex}][name]" class="form-label required">Beneficiary Name ${beneficiaryIndex + 1}</label><input type="text" class="form-control" name="beneficiaries[${beneficiaryIndex}][name]" placeholder="Full name" required></div><div class="col-md-3"><label for="beneficiaries[${beneficiaryIndex}][dob]" class="form-label required">Date of Birth</label><input type="date" class="form-control" name="beneficiaries[${beneficiaryIndex}][dob]" required></div><div class="col-md-3"><label for="beneficiaries[${beneficiaryIndex}][relation]" class="form-label">Relation</label><select class="form-select" name="beneficiaries[${beneficiaryIndex}][relation]"><option value="">Select relation</option><option value="Spouse">Spouse</option><option value="Child">Child</option><option value="Parent">Parent</option><option value="Sibling">Sibling</option><option value="Grandchild">Grandchild</option><option value="Other">Other</option></select></div><div class="col-md-2 d-flex align-items-end"><button type="button" class="btn btn-danger w-100 remove-beneficiary" title="Remove Beneficiary"><i class="bx bx-minus"></i> Remove</button></div>`;
            container.appendChild(newRow);
            beneficiaryIndex++;
            
            // Attach remove handler to new row only
            newRow.querySelector('.remove-beneficiary').addEventListener('click', function(e) {
                e.preventDefault();
                newRow.remove();
            }, { once: true });
        });
    }
    
    // Remove beneficiary (for existing rows)
    function initRemoveButtons() {
        document.querySelectorAll('.remove-beneficiary').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                this.closest('.beneficiary-row').remove();
            }, { once: true });
        });
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initAddButton();
            initRemoveButtons();
        });
    } else {
        initAddButton();
        initRemoveButtons();
    }
})();
</script>

<div class="row g-3 mb-3">

    <div class="col-md-4">
        <label for="bank_name" class="form-label required">Bank Name</label>
        <input type="text" class="form-control" name="bank_name" value="<?php echo e(old('bank_name', $lead->bank_name ?? '')); ?>" placeholder="Bank name" required>
    </div>
    <div class="col-md-4">
        <label for="account_type" class="form-label required">Account Type</label>
        <select class="form-select" name="account_type" required>
            <option value="">Select type</option>
            <option value="Checking" <?php echo e(old('account_type', $lead->account_type ?? '') == 'Checking' ? 'selected' : ''); ?>>Checking</option>
            <option value="Savings" <?php echo e(old('account_type', $lead->account_type ?? '') == 'Savings' ? 'selected' : ''); ?>>Savings</option>
            <option value="Card" <?php echo e(old('account_type', $lead->account_type ?? '') == 'Card' ? 'selected' : ''); ?>>Card</option>
        </select>
    </div>
    <div class="col-md-4">
        <label for="account_number" class="form-label required">Account Number</label>
        <input type="text" class="form-control" name="account_number" value="<?php echo e(old('account_number', $lead->account_number ?? '')); ?>" placeholder="Account number" required>
    </div>
    <div class="col-md-4">
        <label for="routing_number" class="form-label required">Routing Number</label>
        <input type="text" class="form-control" name="routing_number" value="<?php echo e(old('routing_number', $lead->routing_number ?? '')); ?>" placeholder="9 digits" required>
    </div>
    <div class="col-md-6">
        <label for="bank_balance" class="form-label">Bank Balance</label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="number" step="0.01" class="form-control" name="bank_balance" value="<?php echo e(old('bank_balance', $lead->bank_balance ?? '')); ?>" placeholder="Balance">
        </div>
    </div>
</div>

<h6 class="form-section-title"><i class="bx bx-credit-card me-2"></i>Card Info (Optional)</h6>
<div class="row g-3">
    <div class="col-md-6">
        <label for="card_number" class="form-label">Card Number</label>
        <input type="text" class="form-control" name="card_number" value="<?php echo e(old('card_number', $lead->card_number ?? '')); ?>" placeholder="XXXX-XXXX-XXXX-XXXX" maxlength="19">
    </div>
    <div class="col-md-3">
        <label for="cvv" class="form-label">CVV</label>
        <input type="text" class="form-control" name="cvv" value="<?php echo e(old('cvv', $lead->cvv ?? '')); ?>" placeholder="XXX" maxlength="4">
    </div>
    <div class="col-md-3">
        <label for="expiry_date" class="form-label">Expiry</label>
        <input type="text" class="form-control" name="expiry_date" value="<?php echo e(old('expiry_date', $lead->expiry_date ?? '')); ?>">
    </div>
</div>

<h6 class="form-section-title"><i class="bx bx-briefcase me-2"></i>Partner Information</h6>
<div class="row g-3 mb-4">
    <div class="col-md-12">
        <label for="assigned_partner" class="form-label">Assigned Partner</label>
        <input type="text" class="form-control" name="assigned_partner" id="assigned_partner" value="<?php echo e(old('assigned_partner', $lead->assigned_partner ?? '')); ?>" placeholder="Enter partner name">
    </div>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!isset($isValidator)): ?>
<!-- Follow Up Schedule Section -->
<h6 class="form-section-title"><i class="bx bx-calendar-event me-2"></i>Follow Up Schedule</h6>
<div class="row g-3 mb-4">
    <div class="col-md-12">
        <label for="followup_required" class="form-label required">Follow Up Required</label>
        <select class="form-select" name="followup_required" id="followup_required" required>
            <option value="">Select option...</option>
            <option value="1" <?php echo e(old('followup_required', $lead->followup_required ?? '') == '1' ? 'selected' : ''); ?>>Yes</option>
            <option value="0" <?php echo e(old('followup_required', $lead->followup_required ?? '') === '0' || old('followup_required', $lead->followup_required ?? '') === 0 ? 'selected' : ''); ?>>No</option>
        </select>
    </div>
    <div class="col-md-12" id="followup_schedule_fields" style="display: none;">
        <label for="followup_scheduled_at" class="form-label required">Follow Up Date & Time</label>
        <input type="datetime-local" class="form-control" name="followup_scheduled_at" id="followup_scheduled_at" value="<?php echo e(old('followup_scheduled_at', $lead->followup_scheduled_at ? \Carbon\Carbon::parse($lead->followup_scheduled_at)->format('Y-m-d\TH:i') : '')); ?>">
        <small class="text-muted">When should the follow-up call be scheduled?</small>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const followupRequired = document.getElementById('followup_required');
    const followupScheduleFields = document.getElementById('followup_schedule_fields');
    const followupScheduledAt = document.getElementById('followup_scheduled_at');
    
    function toggleFollowupFields() {
        if (followupRequired.value === '1') {
            followupScheduleFields.style.display = 'block';
            followupScheduledAt.setAttribute('required', 'required');
        } else {
            followupScheduleFields.style.display = 'none';
            followupScheduledAt.removeAttribute('required');
        }
    }
    
    // Initialize on page load
    toggleFollowupFields();
    
    // Listen for changes
    followupRequired.addEventListener('change', toggleFollowupFields);
});
</script>

<h6 class="form-section-title"><i class="bx bx-user-check me-2"></i>Assign Validator</h6>
<div class="row g-3">
    <div class="col-md-12">
        <label for="assigned_validator_id" class="form-label required">Select Validator</label>
        <select class="form-select" name="assigned_validator_id" required>
            <option value="">Choose Validator...</option>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $validators ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $validator): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($validator->id); ?>" <?php echo e(old('assigned_validator_id', $lead->assigned_validator_id ?? '') == $validator->id ? 'selected' : ''); ?>>
                    <?php echo e($validator->name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </select>
    </div>
</div>
<?php else: ?>
<!-- Follow Up Schedule Information (Read-Only for Validator) -->
<h6 class="form-section-title"><i class="bx bx-calendar-event me-2"></i>Follow Up Schedule</h6>
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <label class="form-label">Follow Up Required</label>
        <div class="readonly-value">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->followup_required): ?>
                <span class="badge bg-success">Yes</span>
            <?php else: ?>
                <span class="badge bg-secondary">No</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lead->followup_required && $lead->followup_scheduled_at): ?>
    <div class="col-md-6">
        <label class="form-label">Scheduled Date & Time</label>
        <div class="readonly-value">
            <i class="bx bx-calendar me-2"></i><?php echo e(\Carbon\Carbon::parse($lead->followup_scheduled_at)->format('M d, Y h:i A')); ?>

        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php /**PATH /var/www/taurus-crm/resources/views/peregrine/closers/form.blade.php ENDPATH**/ ?>