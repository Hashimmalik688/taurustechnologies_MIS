<?php $__env->startSection('title'); ?>
    Ravens Calling System
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(URL::asset('build/css/app.min.css')); ?>" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="<?php echo e(URL::asset('build/libs/toastr/build/toastr.min.css')); ?>" />
    <style>
        .auto-dial-btn {
            position: relative;
            min-width: 150px;
        }
        .auto-dial-btn.active {
            background: #f46a6a !important;
            border-color: #f46a6a !important;
        }
        .dial-btn {
            transition: all 0.2s;
        }
        .dial-btn:hover {
            transform: scale(1.1);
        }
        .lead-row.calling {
            background-color: rgba(52, 195, 143, 0.1) !important;
            border-left: 3px solid #34c38f;
        }
        .lead-row.dialed {
            opacity: 0.6;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Ravens
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Ravens Calling System
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <div class="col-12">
            <div class="card bordered">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Leads to Call</h4>
                    <div class="d-flex gap-2">
                        <button id="autoDialBtn" class="btn btn-success auto-dial-btn">
                            <i class="bx bx-play-circle me-1"></i>
                            <span id="autoDialText">Start Auto-Dial</span>
                        </button>
                        <button onclick="testZoomProtocol()" class="btn btn-warning btn-sm">
                            <i class="bx bx-test-tube me-1"></i> Test Zoom
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">#</th>
                                    <th>Customer Name</th>
                                    <th width="120" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="leadsTableBody">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="lead-row" data-lead-id="<?php echo e($lead->id); ?>" data-phone="<?php echo e($lead->phone_number); ?>">
                                        <td><?php echo e($index + 1); ?></td>
                                        <td>
                                            <strong><?php echo e($lead->cn_name ?? 'N/A'); ?></strong>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(strtolower($lead->team ?? '') === 'peregrine'): ?>
                                                <span class="badge bg-success ms-2">Paraguin</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-primary btn-sm dial-btn" onclick="makeCall('<?php echo e($lead->id); ?>', '<?php echo e($lead->phone_number); ?>', this)">
                                                <i class="bx bx-phone-call"></i> Call
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            <i class="bx bx-info-circle fs-3"></i>
                                            <p class="mb-0">No leads available</p>
                                        </td>
                                    </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PHASED CALL POPUP MODAL -->
    <div class="modal fade" id="callDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #d4af37 0%, #b8941f 100%);">
                    <h5 class="modal-title text-white"><i class="fas fa-phone-alt me-2"></i><span id="callModalStatus">Call Connected</span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="callModalBody">

                    <!-- PHASE 1: CALL CONNECTED -->
                    <div id="phase1" style="display: none;">
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-phone-alt text-success" style="font-size: 4rem;"></i>
                            </div>
                            <h3 class="mb-3" style="color: #d4af37;" id="callerName">Connecting...</h3>
                            <p class="lead mb-2" id="callerPhone"></p>
                            <p class="text-muted">Call in progress</p>
                            <button type="button" class="btn btn-lg mt-4" style="background: linear-gradient(135deg, #d4af37 0%, #b8941f 100%); color: white;" onclick="goToPhase2()">
                                Start Call Info <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- PHASE 2: ESSENTIAL FIELDS -->
                    <div id="phase2" style="display: none;">
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i> <strong>Review and update information as needed</strong>
                        </div>

                        <div class="row g-3">
                            <!-- Name -->
                            <div class="col-md-6">
                                <label class="form-label small text-muted">CURRENT NAME</label>
                                <div class="p-2 bg-light border rounded mb-2 fw-bold" id="displayName">-</div>
                                <label class="form-label small">Changes (if any)</label>
                                <input type="text" class="form-control" id="phase2_name" placeholder="Enter new name if changed">
                            </div>

                            <!-- Phone Number -->
                            <div class="col-md-6">
                                <label class="form-label small text-muted">CURRENT PHONE</label>
                                <div class="p-2 bg-light border rounded mb-2 fw-bold" id="displayPhone">-</div>
                                <label class="form-label small">Changes (if any)</label>
                                <input type="text" class="form-control" id="phase2_phone" placeholder="Enter new phone if changed">
                            </div>

                            <!-- DOB & SSN -->
                            <div class="col-md-6">
                                <label class="form-label small">Date of Birth</label>
                                <input type="date" class="form-control" id="phase2_dob">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">SSN</label>
                                <input type="text" class="form-control" id="phase2_ssn" placeholder="XXX-XX-XXXX">
                            </div>

                            <!-- Address -->
                            <div class="col-12">
                                <label class="form-label small">Address</label>
                                <input type="text" class="form-control" id="phase2_address" placeholder="Enter address">
                            </div>

                            <!-- Beneficiary -->
                            <div class="col-12">
                                <label class="form-label small text-muted">CURRENT BENEFICIARY</label>
                                <div class="p-2 bg-light border rounded mb-2 fw-bold" id="displayBeneficiary">-</div>
                                <label class="form-label small">Add/Update Beneficiaries</label>
                                <div id="beneficiaries-ravens-container">
                                    <div class="row g-2 mb-2 beneficiary-ravens-row" data-index="0">
                                        <div class="col-md-5">
                                            <input type="text" class="form-control form-control-sm" name="beneficiaries[0][name]" placeholder="Beneficiary Name">
                                        </div>
                                        <div class="col-md-5">
                                            <input type="date" class="form-control form-control-sm" name="beneficiaries[0][dob]" placeholder="DOB">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-success btn-sm w-100" id="add-beneficiary-ravens">
                                                <i class="bx bx-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Carrier, Coverage, Premium -->
                            <div class="col-md-4">
                                <label class="form-label small">Carrier</label>
                                <input type="text" class="form-control" id="phase2_carrier">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Coverage Amount</label>
                                <input type="number" class="form-control" id="phase2_coverage" step="0.01" placeholder="Amount">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Monthly Premium</label>
                                <input type="number" class="form-control" id="phase2_premium" step="0.01" placeholder="Amount">
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-light btn-lg px-4 me-2" onclick="goToPhase1()">
                                <i class="fas fa-arrow-left me-2"></i> Back
                            </button>
                            <button type="button" class="btn btn-lg px-4" style="background: linear-gradient(135deg, #d4af37 0%, #b8941f 100%); color: white;" id="showMoreBtn" onclick="goToPhase3()">
                                <i class="fas fa-arrow-right me-2"></i> Continue
                            </button>
                        </div>
                    </div>

                    <!-- PHASE 3: FULL DETAILS WITH CHANGE TRACKING -->
                    <div id="phase3" style="display: none;">
                        <div class="alert alert-success mb-3">
                            <i class="fas fa-check-circle me-2"></i> All essential fields captured. Review and update complete information below.
                        </div>

                        <div class="row g-3">
                            <!-- Personal Information Section -->
                            <div class="col-12">
                                <h5 class="border-bottom pb-2 mb-3" style="color: #d4af37;">Personal Information</h5>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Name:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_name"></div>
                                <input type="text" class="form-control form-control-sm" id="change_name" placeholder="Changes (if any, write same as above if no change)">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Phone Number:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_phone"></div>
                                <input type="text" class="form-control form-control-sm" id="change_phone" placeholder="Changes (if any, write same as above if no change)">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Date of Birth:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_dob"></div>
                                <input type="date" class="form-control form-control-sm" id="change_dob">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Gender:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_gender"></div>
                                <select class="form-select form-select-sm" id="change_gender">
                                    <option value="">Select</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Birth Place:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_birthplace"></div>
                                <input type="text" class="form-control form-control-sm" id="change_birthplace" placeholder="Changes (if any)">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">SSN:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_ssn"></div>
                                <input type="text" class="form-control form-control-sm" id="change_ssn" placeholder="Changes (if any)">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Smoker:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_smoker"></div>
                                <select class="form-select form-select-sm" id="change_smoker">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Height & Weight:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_height_weight"></div>
                                <input type="text" class="form-control form-control-sm" id="change_height_weight" placeholder="e.g., 5'10\", 180 lbs">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Address:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_address"></div>
                                <input type="text" class="form-control form-control-sm" id="change_address" placeholder="Changes (if any)">
                            </div>

                            <!-- Medical Information Section -->
                            <div class="col-12 mt-4">
                                <h5 class="border-bottom pb-2 mb-3" style="color: #d4af37;">Medical Information</h5>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Medical Issue:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_medical_issue"></div>
                                <textarea class="form-control form-control-sm" id="change_medical_issue" rows="2" placeholder="Changes (if any)"></textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Medications:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_medications"></div>
                                <textarea class="form-control form-control-sm" id="change_medications" rows="2" placeholder="Changes (if any)"></textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Doctor Name:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_doctor"></div>
                                <input type="text" class="form-control form-control-sm" id="change_doctor" placeholder="Changes (if any)">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Doctor Address:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_doctor_address"></div>
                                <input type="text" class="form-control form-control-sm" id="change_doctor_address" placeholder="Changes (if any)">
                            </div>

                            <!-- Policy Information Section -->
                            <div class="col-12 mt-4">
                                <h5 class="border-bottom pb-2 mb-3" style="color: #d4af37;">Policy Information</h5>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Beneficiary:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_beneficiary"></div>
                                <input type="text" class="form-control form-control-sm" id="change_beneficiary" placeholder="Changes (if any)">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Beneficiary DOB:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_beneficiary_dob"></div>
                                <input type="date" class="form-control form-control-sm" id="change_beneficiary_dob">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Policy Type:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_policy_type"></div>
                                <input type="text" class="form-control form-control-sm" id="change_policy_type" placeholder="Changes (if any)">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Carrier:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_carrier"></div>
                                <input type="text" class="form-control form-control-sm" id="change_carrier" placeholder="Changes (if any)">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Coverage Amount:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_coverage"></div>
                                <input type="number" class="form-control form-control-sm" id="change_coverage" step="0.01" placeholder="Changes (if any)">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Monthly Premium:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_premium"></div>
                                <input type="number" class="form-control form-control-sm" id="change_premium" step="0.01" placeholder="Changes (if any)">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Initial Draft Date:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_draft_date"></div>
                                <input type="date" class="form-control form-control-sm" id="change_draft_date">
                            </div>

                            <!-- Banking Information Section -->
                            <div class="col-12 mt-4">
                                <h5 class="border-bottom pb-2 mb-3" style="color: #d4af37;">Banking Information</h5>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Bank Name:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_bank_name"></div>
                                <input type="text" class="form-control form-control-sm" id="change_bank_name" placeholder="Changes (if any)">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Account Type:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_account_type"></div>
                                <select class="form-select form-select-sm" id="change_account_type">
                                    <option value="">Select</option>
                                    <option value="Checking">Checking</option>
                                    <option value="Savings">Savings</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Routing Number:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_routing"></div>
                                <input type="text" class="form-control form-control-sm" id="change_routing" placeholder="Changes (if any)">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Account Number:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_account"></div>
                                <input type="text" class="form-control form-control-sm" id="change_account" placeholder="Changes (if any)">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Verified By:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_verified_by"></div>
                                <input type="text" class="form-control form-control-sm" id="change_verified_by" placeholder="Changes (if any)">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Bank Balance:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_balance"></div>
                                <input type="number" class="form-control form-control-sm" id="change_balance" step="0.01" placeholder="Changes (if any)">
                            </div>

                            <!-- Additional Information -->
                            <div class="col-12 mt-4">
                                <h5 class="border-bottom pb-2 mb-3" style="color: #d4af37;">Additional Information</h5>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Source:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_source"></div>
                                <input type="text" class="form-control form-control-sm" id="change_source" placeholder="Changes (if any)">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Closer Name:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_closer"></div>
                                <input type="text" class="form-control form-control-sm" id="change_closer" placeholder="Changes (if any)">
                            </div>

                            <!-- Sale Assignment Section (moved from Phase 2) -->
                            <div class="col-12 mt-4">
                                <h5 class="border-bottom pb-2 mb-3" style="color: #d4af37;"><i class="fas fa-user-tag me-2"></i>Sale Assignment</h5>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Policy Carrier:</label>
                                <select class="form-select" id="phase3_policy_carrier">
                                    <option value="">Select Carrier</option>
                                    <option value="AMAM">AMAM</option>
                                    <option value="Combined">Combined</option>
                                    <option value="AIG">AIG</option>
                                    <option value="LBL">LBL</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">State:</label>
                                <select class="form-select" id="phase3_approved_state">
                                    <option value="">Select State</option>
                                    <option value="FL">Florida</option>
                                    <option value="TX">Texas</option>
                                    <option value="CA">California</option>
                                    <option value="NY">New York</option>
                                    <option value="PA">Pennsylvania</option>
                                </select>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-secondary" onclick="goToPhase2()">
                                <i class="fas fa-arrow-left me-2"></i> Back to Essential Fields
                            </button>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <!-- Disposition Dropdown (on the left) -->
                    <div class="btn-group dropup me-auto">
                        <button type="button" class="btn btn-danger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ban me-1"></i> Dispose Lead
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="disposeCurrentLead('no_answer'); return false;"><i class="fas fa-phone-slash me-2"></i> No Answer</a></li>
                            <li><a class="dropdown-item" href="#" onclick="disposeCurrentLead('wrong_number'); return false;"><i class="fas fa-phone-times me-2"></i> Wrong Number</a></li>
                            <li><a class="dropdown-item" href="#" onclick="disposeCurrentLead('wrong_details'); return false;"><i class="fas fa-exclamation-triangle me-2"></i> Wrong Details</a></li>
                        </ul>
                    </div>
                    
                    <!-- Action buttons (on the right) -->
                    <button type="button" class="btn btn-secondary" onclick="closeCallModal()"><i class="fas fa-phone-slash me-1"></i> End Call</button>
                    <button type="button" class="btn btn-warning" onclick="saveAndExit()"><i class="fas fa-save me-1"></i> Save & Exit</button>
                    <button type="button" class="btn btn-success" onclick="submitSale()"><i class="fas fa-check-circle me-1"></i> Submit Sale</button>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="<?php echo e(URL::asset('build/libs/toastr/build/toastr.min.js')); ?>"></script>
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.0/dist/echo.iife.js"></script>
<script>
    // Use window scope for global variables to avoid conflicts
    window.autoDialActive = false;
    window.currentLeadIndex = 0;
    window.dialedLeads = new Set();
    window.isCallActive = false;
    window.autoDialTimeout = null;
    window.currentEventId = null;
    window.pollInterval = null;
    window.currentLeadData = null;

    // TEST: Ensure JavaScript is loading
    console.log('✅ Ravens calling script loaded');
    
    // Test function to verify modal functionality
    window.testRavensModal = function() {
        console.log('🧪 Testing Ravens modal functionality...');
        const modalElement = document.getElementById('callDetailsModal');
        console.log('🎭 Modal element:', modalElement);
        
        if (modalElement) {
            try {
                const modal = new bootstrap.Modal(modalElement);
                console.log('✅ Bootstrap modal object created successfully');
                modal.show();
                console.log('✅ Modal.show() called - modal should be visible');
                
                // Auto-close after 3 seconds for testing
                setTimeout(() => {
                    modal.hide();
                    console.log('🚪 Test modal closed automatically');
                }, 3000);
                
            } catch (error) {
                console.error('❌ Error in modal test:', error);
            }
        } else {
            console.error('❌ Modal element not found!');
        }
    };
    
    // Direct modal test with data - bypasses API call
    window.testRavensModalWithData = function() {
        console.log('🧪 Testing Ravens modal with sample data (bypassing API)...');
        
        const testCallData = {
            event_id: 'test-' + Date.now(),
            lead_data: {
                id: 999,
                cn_name: 'Test Customer',
                phone_number: '1234567890',
                date_of_birth: '1990-01-01',
                ssn: '123-45-6789'
            },
            lead_id: 999,
            status: 'connected'
        };
        
        console.log('🧪 Calling showCallModal directly with test data...');
        if (typeof showCallModal === 'function') {
            showCallModal(testCallData);
        } else {
            console.error('❌ showCallModal function not found!');
        }
    };
    
    // Make test available in console
    console.log('💡 Test commands available:');
    console.log('  - testRavensModal() - Basic modal test');
    console.log('  - testRavensModalWithData() - Full modal test with data');

    // Unified call function - uses proper Zoom API integration
    // Shows Ravens form after 10-second delay when call is initiated
    window.makeCall = function(leadId, phoneNumber, button) {
        console.log('makeCall called with:', leadId, phoneNumber);
        
        if (!phoneNumber) {
            alert('No phone number available for this lead');
            return;
        }

        // Show loading state
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Connecting...';

        // Use the proper Zoom API integration
        fetch(`/zoom/dial/${leadId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Zoom API response:', data);
            
            if (data.success) {
                console.log('✅ Desktop call initiated - Zoom will fire webhooks when call connects');
                
                // Open Zoom Phone desktop app using an invisible link click
                // This prevents page navigation and keeps polling active
                if (data.zoom_url) {
                    const link = document.createElement('a');
                    link.href = data.zoom_url;
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    console.log('📞 Zoom Phone link clicked:', data.zoom_url);
                }
                
                // Mark lead as dialed
                window.dialedLeads.add(leadId);
                
                // Update UI
                const row = button.closest('.lead-row');
                if (row) {
                    row.classList.add('calling');
                    setTimeout(() => {
                        row.classList.remove('calling');
                        row.classList.add('dialed');
                    }, 1000);
                }
                
                console.log('📞 Call initiated - Opening Zoom Phone for ' + data.lead_name);
                
                // Start monitoring for webhook-triggered status updates
                startRealCallDetection(leadId, phoneNumber, data.lead_name);
                
            } else {
                if (data.error && data.error.includes('not authorized')) {
                    alert('❌ Zoom Not Connected\n\nRedirecting to connect your Zoom account...');
                    window.location.href = '/zoom/authorize';
                } else {
                    alert(`❌ API Error: ${data.error || 'Unknown error'}`);
                }
            }
        })
        .catch(error => {
            console.error('API request failed:', error);
            alert('❌ Connection failed. Please try again.');
        })
        .finally(() => {
            button.disabled = false;
            button.innerHTML = originalText;
        });
    }

    // Get user's zoom number
    window.zoomNumber = '<?php echo e(Auth::user()->zoom_number ?? ''); ?>';
    window.sanitizedZoomNumber = '<?php echo e(Auth::user()->sanitized_zoom_number ?? ''); ?>';

    // Echo disabled - using API-based call monitoring instead
    // Safety check for echo references
    if (window.Echo && typeof window.Echo.channel === 'function') {
        console.log('Echo available but disabled for stability');
    }
    console.log('Echo disabled - using API-based call monitoring instead');

    // Auto-dial toggle button
    document.getElementById('autoDialBtn').addEventListener('click', function() {
        window.autoDialActive = !window.autoDialActive;
        const btn = this;
        const text = document.getElementById('autoDialText');
        const icon = btn.querySelector('i');

        if (window.autoDialActive) {
            btn.classList.add('active');
            text.textContent = 'Stop Auto-Dial';
            icon.className = 'bx bx-stop-circle me-1';
            window.currentLeadIndex = 0;
            autoDialNext();
        } else {
            btn.classList.remove('active');
            text.textContent = 'Start Auto-Dial';
            icon.className = 'bx bx-play-circle me-1';
            if (autoDialTimeout) {
                clearTimeout(autoDialTimeout);
            }
            // Remove calling highlight
            document.querySelectorAll('.lead-row').forEach(row => {
                row.classList.remove('calling');
            });
        }
    });

    // Auto-dial next lead
    function autoDialNext() {
        if (!window.autoDialActive) return;
        if (window.isCallActive) {
            console.log('Call in progress, waiting...');
            return;
        }

        const rows = document.querySelectorAll('.lead-row');

        // Find next undailed lead
        while (window.currentLeadIndex < rows.length) {
            const row = rows[window.currentLeadIndex];
            const leadId = row.dataset.leadId;

            if (!window.dialedLeads.has(leadId)) {
                // Found undailed lead, dial it
                const phone = row.dataset.phone;
                const dialBtn = row.querySelector('.dial-btn');

                // Highlight current row
                document.querySelectorAll('.lead-row').forEach(r => r.classList.remove('calling'));
                row.classList.add('calling');

                // Scroll to current lead
                row.scrollIntoView({ behavior: 'smooth', block: 'center' });

                // Dial the lead
                makeCall(leadId, phone, dialBtn);
                return;
            }

            window.currentLeadIndex++;
        }

        // All leads dialed
        window.autoDialActive = false;
        const btn = document.getElementById('autoDialBtn');
        const text = document.getElementById('autoDialText');
        const icon = btn.querySelector('i');
        btn.classList.remove('active');
        text.textContent = 'Start Auto-Dial';
        icon.className = 'bx bx-play-circle me-1';

        alert('All leads have been dialed!');
    }

    // Test function for Zoom protocol
    function testZoomProtocol() {
        console.log('Testing Zoom protocol...');
        
        const testNumber = '2393871921'; // Hashim's number
        const zoomUrl = 'zoomphonecall://' + testNumber;
        
        console.log('Test Zoom URL:', zoomUrl);
        
        // Show confirmation
        const confirmed = confirm(`Testing Zoom Phone protocol with number: ${testNumber}\n\nThis will attempt to dial Hashim Shabbir.\n\nClick OK to test, Cancel to abort.`);
        
        if (confirmed) {
            toastr.info('Testing Zoom Phone protocol...', 'Test Mode');
            
            try {
                // Try multiple methods
                console.log('Method 1: window.location.href');
                window.location.href = zoomUrl;
                
                setTimeout(() => {
                    console.log('Method 2: Creating a link and clicking it');
                    const link = document.createElement('a');
                    link.href = zoomUrl;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }, 500);
                
                setTimeout(() => {
                    console.log('Method 3: Using iframe');
                    const iframe = document.createElement('iframe');
                    iframe.style.display = 'none';
                    iframe.src = zoomUrl;
                    document.body.appendChild(iframe);
                    
                    setTimeout(() => {
                        document.body.removeChild(iframe);
                    }, 1000);
                }, 1000);
                
            } catch (error) {
                console.error('Error testing Zoom:', error);
                alert('Error: ' + error.message);
            }
        }
    }

    // New flow: Show form after 10 seconds, monitor for call end
    window.startRealCallDetection = function(leadId, phoneNumber, leadName) {
        console.log("📞 Starting call detection (show form after 10 seconds)...", { leadId, phoneNumber, leadName });
        
        let isMonitoringActive = true;
        let formShown = false;
        let checkInterval = null;
        
        // Store current call info
        window.currentCallInfo = { leadId, phoneNumber, leadName };
        
        // Show form after 10 seconds
        const formTimer = setTimeout(() => {
            if (isMonitoringActive) {
                console.log('⏰ 10 seconds passed - showing Ravens form');
                formShown = true;
                showRavensFormForCall(leadId, phoneNumber, leadName, 'connected', 0);
            }
        }, 10000); // 10 seconds
        
        // Start polling to detect call end (but only close form if it's already shown)
        function checkCallStatus() {
            if (!isMonitoringActive) {
                console.log('⏹️ Monitoring stopped - clearing interval');
                if (checkInterval) clearInterval(checkInterval);
                return;
            }
            
            console.log(`🔍 Checking call status for lead ${leadId}... (form shown: ${formShown})`);
            
            fetch(`/zoom/call-status/${leadId}`)
                .then(response => response.json())
                .then(data => {
                    console.log('📊 Call status response:', data);
                    
                    if (data.success) {
                        const status = data.status || data.call_status;
                        console.log(`Current status: ${status}`);
                        
                        // Check for ended states - comprehensive webhook detection
                        if (status === 'ended' || status === 'completed' || status === 'failed' || 
                            status === 'cancelled' || status === 'missed' || status === 'voicemail' || 
                            status === 'rejected' || status === 'busy' || status === 'hangup' || 
                            status === 'disconnected' || status === 'timeout') {
                            
                            console.log(`❌ Call ended detected via webhook! Status: ${status}, Form shown: ${formShown}`);
                            
                            // Only close form if it's already shown
                            if (formShown) {
                                console.log('🚪 Closing form - call ended after form was shown');
                                isMonitoringActive = false;
                                if (checkInterval) clearInterval(checkInterval);
                                closeRavensForm();
                                toastr.info(`Call ended`, 'Call Completed');
                            } else {
                                // Form not shown yet - just stop monitoring, form will never appear
                                console.log('⛔ Call ended before form shown - canceling timer');
                                isMonitoringActive = false;
                                clearTimeout(autoShowTimer);
                                if (checkInterval) clearInterval(checkInterval);
                                toastr.info(`Call ${status}`, 'Call Ended');
                            }
                        } else {
                            console.log(`✅ Call still active (status: ${status})`);
                        }
                    } else {
                        console.warn('⚠️ Status check returned success=false');
                    }
                })
                .catch(error => {
                    console.error('❌ Status check failed:', error);
                });
        }
        
        // Poll every 2 seconds to detect call end
        console.log('▶️ Starting status polling every 2 seconds');
        checkInterval = setInterval(checkCallStatus, 2000);
        
        // Initial check immediately
        checkCallStatus();
        
        // Cleanup after 10 minutes
        setTimeout(() => {
            if (isMonitoringActive) {
                console.log('⏰ Call monitoring timeout - stopping detection');
                isMonitoringActive = false;
                clearTimeout(formTimer);
                if (checkInterval) clearInterval(checkInterval);
            }
        }, 600000); // 10 minutes
    }
    
    // Close the Ravens form when call ends
    window.closeRavensForm = function() {
        console.log('🚪 Closing Ravens form');
        const modalElement = document.getElementById('callDetailsModal');
        if (modalElement) {
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
        }
        window.currentCallInfo = null;
        window.currentLeadData = null;
    }
    
    // Show Ravens form when call is connected
    window.showRavensFormForCall = function(leadId, phoneNumber, leadName, callStatus, duration) {
        console.log(`🎯 Showing Ravens form: ${leadName}`);
        console.log('🔍 Debug: leadId=' + leadId + ', phoneNumber=' + phoneNumber + ', callStatus=' + callStatus);
        
        toastr.success(`Opening form for: ${leadName}`, 'Call Form');
        
        // Fetch full lead data from the server to populate the form
        console.log('🌐 Fetching lead data from:', `/ravens/leads/${leadId}/data`);
        fetch(`/ravens/leads/${leadId}/data`)
            .then(response => {
                console.log('🌐 API Response Status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(leadData => {
                console.log('📋 Got full lead data:', leadData);
                console.log('🔍 Checking if showCallModal function exists...');
                console.log('showCallModal type:', typeof showCallModal);
                
                // Create proper call event data for the modal
                const callEventData = {
                    event_id: 'call_' + Date.now(),
                    lead_data: {
                        id: leadData.id || leadId,
                        cn_name: leadData.cn_name || leadData.name || leadName,
                        phone_number: leadData.phone_number || phoneNumber,
                        date_of_birth: leadData.date_of_birth || '',
                        ssn: leadData.ssn || '',
                        gender: leadData.gender || '',
                        beneficiaries: leadData.beneficiaries || [],
                        carrier_name: leadData.carrier_name || leadData.carrier || '',
                        coverage_amount: leadData.coverage_amount || leadData.coverage || '',
                        monthly_premium: leadData.monthly_premium || leadData.premium || '',
                        birth_place: leadData.birth_place || '',
                        smoker: leadData.smoker || 0,
                        height_weight: leadData.height_weight || '',
                        address: leadData.address || '',
                        medical_issue: leadData.medical_issue || '',
                        medications: leadData.medications || '',
                        doctor_name: leadData.doctor_name || '',
                        doctor_address: leadData.doctor_address || '',
                        policy_type: leadData.policy_type || '',
                        initial_draft_date: leadData.initial_draft_date || '',
                        bank_name: leadData.bank_name || '',
                        account_type: leadData.account_type || '',
                        routing_number: leadData.routing_number || '',
                        account_number: leadData.account_number || '',
                        verified_by: leadData.verified_by || '',
                        bank_balance: leadData.bank_balance || '',
                        source: leadData.source || '',
                        closer_name: leadData.closer_name || ''
                    },
                    lead_id: leadId,
                    status: callStatus,
                    caller_number: 'User',
                    callee_number: phoneNumber,
                    call_connected_at: new Date().toISOString()
                };
                
                // Show the Ravens form modal with full data
                if (typeof showCallModal === 'function') {
                    console.log('✅ Opening Ravens form with full lead data');
                    console.log('🔍 Call event data:', callEventData);
                    showCallModal(callEventData);
                } else {
                    console.error('❌ showCallModal function not found! Redirecting to lead details');
                    // Fallback - redirect to lead details
                    window.location.href = `/ravens/leads/${leadId}`;
                }
            })
            .catch(error => {
                console.error('❌ Failed to fetch lead data:', error);
                console.error('API endpoint might be broken or lead ID invalid');
                
                // Try to show modal anyway with minimal data
                console.log('🎭 Attempting to show modal with minimal data...');
                const callEventData = {
                    event_id: 'call_' + Date.now(),
                    lead_data: {
                        id: leadId,
                        cn_name: leadName,
                        phone_number: phoneNumber
                    },
                    lead_id: leadId,
                    status: callStatus
                };

                if (typeof showCallModal === 'function') {
                    showCallModal(callEventData);
                } else {
                    console.error('❌ showCallModal function not available, redirecting to lead details');
                    window.location.href = `/ravens/leads/${leadId}`;
                }
            });
    }
    
    // Check if call was actually connected (not just ringing/rejected)
    function checkIfCallWasConnected(leadId, phoneNumber, leadName, callDuration) {
        console.log(`Checking if call was connected. Duration: ${callDuration}ms`);
        
        // Use API to determine if call was actually connected
        fetch('/api/call-status/check', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                lead_id: leadId,
                call_duration: callDuration,
                user_interacted: true // User returned focus, indicating interaction
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Call status check result:', data);
            
            if (data.is_connected) {
                console.log(`Call confirmed as connected (confidence: ${data.confidence}%) - showing Ravens form`);
                
                // Get lead data for Ravens form
                return fetch('/api/leads/' + leadId, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });
            } else {
                console.log(`Call likely not connected (confidence: ${data.confidence}%) - no Ravens form`);
                return null;
            }
        })
        .then(response => {
            if (!response) return null;
            return response.json();
        })
        .then(leadData => {
            if (!leadData) return;
            
            const callEventData = {
                event_id: "connected-call-" + Date.now(),
                lead_data: leadData,
                lead_id: leadId,
                status: "connected",
                caller_number: "User",
                callee_number: phoneNumber,
                call_connected_at: new Date().toISOString()
            };
            
            if (typeof showCallModal === "function") {
                console.log("Opening Ravens form for:", leadName || "Lead #" + leadId);
                showCallModal(callEventData);
            } else {
                console.error("showCallModal function not available");
            }
        })
        .catch(error => {
            console.error('Call status check failed:', error);
            // Fallback to simple timing if API fails
            if (callDuration >= 15000) { // More conservative fallback
                console.log('API failed, using fallback timing detection');
                // Simple fallback implementation here if needed
            }
        });
    }
    
    // Open Ravens form with API call data
    function openRavensFormViaApi(leadId, phoneNumber) {
        fetch(`/api/leads/${leadId}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch lead data');
            }
            return response.json();
        })
        .then(leadData => {
            const callEventData = {
                event_id: 'api-call-' + Date.now(),
                lead_data: leadData,
                call_connected_at: new Date().toISOString(),
                caller_number: 'API User',
                callee_number: phoneNumber
            };
            
            console.log('Opening Ravens form via API for:', leadData.cn_name || 'Lead #' + leadId);
            
            if (typeof showCallModal === 'function') {
                showCallModal(callEventData);
                alert('✅ Ravens Form Opened!\n\nFill out the details while on your call.');
            } else {
                alert('✅ Call detected but Ravens form unavailable. Please refresh the page.');
            }
        })
        .catch(error => {
            console.error('Failed to open Ravens form:', error);
            alert('❌ Could not open Ravens form. Please try refreshing the page.');
        });
    }
    
    // Smart call connection detection (fallback method)
    function detectCallConnection(leadId, phoneNumber) {
        let checkCount = 0;
        const maxChecks = 20; // Check for up to 40 seconds
        
        // Store original page title and visibility state
        const originalTitle = document.title;
        const originalVisibility = document.visibilityState;
        
        console.log('Starting call connection detection...');
        
        // Method 1: Monitor page/window focus changes
        const handleVisibilityChange = () => {
            if (document.visibilityState === 'visible' && window.isCallActive === false) {
                console.log('Page became visible - user likely returned from Zoom');
                // Wait a moment, then check if call is connected
                setTimeout(() => {
                    checkCallConnection();
                }, 1000);
            }
        };
        
        // Method 2: Monitor window focus
        const handleWindowFocus = () => {
            if (window.isCallActive === false) {
                console.log('Window gained focus - user likely returned from call');
                setTimeout(() => {
                    checkCallConnection();
                }, 500);
            }
        };
        
        // Method 3: Periodic intelligent checking
        const checkCallConnection = () => {
            if (window.isCallActive) return; // Already handled
            
            checkCount++;
            console.log(`Call connection check ${checkCount}/${maxChecks}`);
            
            // Ask user if call is connected (more intelligently timed)
            const message = `Call connection check for ${phoneNumber}:

` +
                          `• Is your call currently connected?
` +
                          `• If yes, click OK to open Ravens form
` +
                          `• If no, click Cancel to keep waiting
` +
                          `• Check ${checkCount} of ${maxChecks}`;
            
            const isConnected = confirm(message);
            
            if (isConnected) {
                window.isCallActive = true;
                cleanup();
                openRavensForm(leadId, phoneNumber);
            } else if (checkCount >= maxChecks) {
                // Final attempt
                const forceOpen = confirm('Maximum checks reached. Open Ravens form anyway?');
                if (forceOpen) {
                    window.isCallActive = true;
                    cleanup();
                    openRavensForm(leadId, phoneNumber);
                } else {
                    cleanup();
                    console.log('Call connection detection stopped by user');
                }
            } else {
                // Continue checking with increasing delays
                const delay = Math.min(2000 + (checkCount * 500), 8000); // 2s to 8s delays
                setTimeout(checkCallConnection, delay);
            }
        };
        
        // Cleanup function
        const cleanup = () => {
            document.removeEventListener('visibilitychange', handleVisibilityChange);
            window.removeEventListener('focus', handleWindowFocus);
            document.title = originalTitle;
        };
        
        // Set up event listeners
        document.addEventListener('visibilitychange', handleVisibilityChange);
        window.addEventListener('focus', handleWindowFocus);
        
        // Change page title to indicate call in progress
        document.title = '📞 Call in Progress - ' + originalTitle;
        
        // Start the first check after initial delay
        setTimeout(checkCallConnection, 5000); // Wait 5 seconds initially
    }
    
    // Fallback clipboard copy function
    function fallbackCopy(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            alert('Phone number copied to clipboard!');
        } catch (err) {
            alert('Could not copy to clipboard. Please manually copy: ' + text);
        }
        document.body.removeChild(textArea);
    }
    
    // Open Ravens form
    function openRavensForm(leadId, phoneNumber) {
        // Create mock call data for the Ravens form
        const callEventData = {
            event_id: 'manual-dial-' + Date.now(),
            lead_data: {
                id: leadId,
                phone_number: phoneNumber,
                cn_name: 'Lead #' + leadId
            },
            call_connected_at: new Date().toISOString(),
            caller_number: phoneNumber,
            callee_number: phoneNumber
        };
        
        console.log('Opening Ravens form for lead:', leadId);
        if (typeof showCallModal === 'function') {
            showCallModal(callEventData);
            alert('Ravens form opened! Fill out the details while on the call.');
        } else {
            alert('Ravens form function not available. Please refresh the page.');
        }
    }

    // ===== LOCAL POLLING SYSTEM FOR CALL POPUP (DEPRECATED - Using Zoom API polling instead) =====
    // Disabled in favor of aggressive Zoom API polling in startRealCallDetection()
    
    // function startCallPolling() {
    //     console.log('Starting call event polling...');
    //     window.pollInterval = setInterval(checkForCallEvents, 2000);
    //     checkForCallEvents(); // Check immediately
    // }

    // function checkForCallEvents() {
    //     fetch('/api/call-events/poll', {
    //         headers: {
    //             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    //             'Accept': 'application/json'
    //         }
    //     })
    //     .then(response => response.json())
    //     .then(data => {
    //         if (data.has_call && data.event_id !== window.currentEventId) {
    //             window.currentEventId = data.event_id;
    //             showCallModal(data);
    //         }
    //     })
    //     .catch(error => console.error('Polling error:', error));
    // }

    // ===== PHASE NAVIGATION SYSTEM =====
    // Note: currentLeadData is already declared globally at line 477 as window.currentLeadData

    function closeCallModal() {
        const modalElement = document.getElementById('callDetailsModal');
        if (modalElement) {
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            } else {
                // Fallback if modal instance not found
                modalElement.classList.remove('show');
                modalElement.style.display = 'none';
                document.body.classList.remove('modal-open');
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) backdrop.remove();
            }
        }
        console.log('Call modal closed');
    }

    function goToPhase1() {
        console.log('📋 Switching to Phase 1');
        
        const phase1 = document.getElementById('phase1');
        const phase2 = document.getElementById('phase2');
        const phase3 = document.getElementById('phase3');
        
        console.log('🔍 Phase elements found:', {
            phase1: !!phase1,
            phase2: !!phase2,
            phase3: !!phase3
        });
        
        if (phase1) {
            phase1.style.display = 'block';
            console.log('✅ Phase 1 set to display: block');
        } else {
            console.error('❌ Phase 1 element not found!');
        }
        
        if (phase2) phase2.style.display = 'none';
        if (phase3) phase3.style.display = 'none';
        
        console.log('📋 Phase 1 should now be visible');
    }

    function goToPhase2() {
        document.getElementById('phase1').style.display = 'none';
        document.getElementById('phase2').style.display = 'block';
        document.getElementById('phase3').style.display = 'none';
    }

    function goToPhase3() {
        populatePhase3WithData();
        document.getElementById('phase1').style.display = 'none';
        document.getElementById('phase2').style.display = 'none';
        document.getElementById('phase3').style.display = 'block';
    }

    function populatePhase3WithData() {
        const ld = window.currentLeadData;

        // Helper to format date for display
        const formatDate = (dateStr) => {
            if (!dateStr) return 'N/A';
            try {
                const date = new Date(dateStr);
                return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
            } catch {
                return dateStr || 'N/A';
            }
        };

        // Helper to format date for input (YYYY-MM-DD)
        const formatDateInput = (dateStr) => {
            if (!dateStr) return '';
            try {
                if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) return dateStr;
                const date = new Date(dateStr);
                return date.toISOString().split('T')[0];
            } catch {
                return '';
            }
        };

        // Personal Information
        document.getElementById('orig_name').textContent = ld.cn_name || 'N/A';
        document.getElementById('orig_phone').textContent = ld.phone_number || 'N/A';
        document.getElementById('orig_dob').textContent = formatDate(ld.date_of_birth);
        document.getElementById('orig_gender').textContent = ld.gender || 'N/A';
        document.getElementById('orig_birthplace').textContent = ld.birth_place || 'N/A';
        document.getElementById('orig_ssn').textContent = document.getElementById('phase2_ssn').value || ld.ssn || 'N/A';
        document.getElementById('orig_smoker').textContent = ld.smoker == 1 ? 'Yes' : 'No';
        document.getElementById('orig_height_weight').textContent = ld.height_weight || 'N/A';
        // Address fallback: use address, else state, else birth place
        let addressDisplay3 = ld.address;
        if (!addressDisplay3 || addressDisplay3.trim() === '') {
            addressDisplay3 = ld.state || ld.birth_place || 'N/A';
        }
        document.getElementById('orig_address').textContent = addressDisplay3;

        // Medical Information
        document.getElementById('orig_medical_issue').textContent = ld.medical_issue || 'N/A';
        document.getElementById('orig_medications').textContent = ld.medications || 'N/A';
        document.getElementById('orig_doctor').textContent = ld.doctor_name || 'N/A';
        document.getElementById('orig_doctor_address').textContent = ld.doctor_address || 'N/A';

        // Policy Information
        // Show all current beneficiaries (names and DOBs if available)
        let beneficiariesDisplay = 'N/A';
        if (ld.beneficiaries && ld.beneficiaries.length > 0) {
            beneficiariesDisplay = ld.beneficiaries.map(b => {
                if (b.dob) {
                    return b.name + ' (' + formatDate(b.dob) + ')';
                }
                return b.name;
            }).join(', ');
        }
        document.getElementById('orig_beneficiary').textContent = beneficiariesDisplay;
        // Show first beneficiary DOB if available
        let beneficiaryDob = '';
        if (ld.beneficiaries && ld.beneficiaries.length > 0 && ld.beneficiaries[0].dob) {
            beneficiaryDob = formatDate(ld.beneficiaries[0].dob);
        } else if (ld.beneficiary_dob) {
            beneficiaryDob = formatDate(ld.beneficiary_dob);
        } else {
            beneficiaryDob = 'N/A';
        }
        document.getElementById('orig_beneficiary_dob').textContent = beneficiaryDob;
        document.getElementById('orig_policy_type').textContent = ld.policy_type || 'N/A';
        document.getElementById('orig_carrier').textContent = document.getElementById('phase2_carrier').value || 'N/A';
        document.getElementById('orig_coverage').textContent = document.getElementById('phase2_coverage').value ? '$' + parseFloat(document.getElementById('phase2_coverage').value).toLocaleString() : 'N/A';
        document.getElementById('orig_premium').textContent = document.getElementById('phase2_premium').value ? '$' + parseFloat(document.getElementById('phase2_premium').value).toFixed(2) : 'N/A';
        document.getElementById('orig_draft_date').textContent = formatDate(ld.initial_draft_date);

        // Banking Information
        document.getElementById('orig_bank_name').textContent = ld.bank_name || 'N/A';
        document.getElementById('orig_account_type').textContent = ld.account_type || 'N/A';
        document.getElementById('orig_routing').textContent = ld.routing_number || 'N/A';
        document.getElementById('orig_account').textContent = ld.account_number || 'N/A';
        document.getElementById('orig_verified_by').textContent = ld.verified_by || 'N/A';
        document.getElementById('orig_balance').textContent = ld.bank_balance ? '$' + parseFloat(ld.bank_balance).toFixed(2) : 'N/A';

        // Additional Information
        document.getElementById('orig_source').textContent = ld.source || 'N/A';
        document.getElementById('orig_closer').textContent = ld.closer_name || 'N/A';

        // Pre-fill change inputs with Phase 2 data
        document.getElementById('change_name').value = ld.cn_name || '';
        document.getElementById('change_phone').value = ld.phone_number || '';
        document.getElementById('change_dob').value = formatDateInput(document.getElementById('phase2_dob').value);
        document.getElementById('change_ssn').value = document.getElementById('phase2_ssn').value || '';
        // Beneficiary is now handled separately in beneficiaries array
        document.getElementById('change_carrier').value = document.getElementById('phase2_carrier').value || '';
        document.getElementById('change_coverage').value = document.getElementById('phase2_coverage').value || '';
        document.getElementById('change_premium').value = document.getElementById('phase2_premium').value || '';
    }

    function validatePhase2Fields() {
        // All fields are now optional, always enable the Continue button
        const showMoreBtn = document.getElementById('showMoreBtn');
        if (showMoreBtn) {
            showMoreBtn.disabled = false;
            showMoreBtn.classList.remove('btn-secondary');
        }
    }

    function showCallModal(callData) {
        console.log('=== CALL CONNECTED ===', callData);
        console.log('🔍 Attempting to show Ravens modal...');
        
        const leadData = callData.lead_data;
        window.currentLeadData = leadData;

        // PHASE 1: Show caller identification - Check if elements exist first
        const callerNameEl = document.getElementById('callerName');
        const callerPhoneEl = document.getElementById('callerPhone');
        
        if (callerNameEl) {
            callerNameEl.textContent = leadData.cn_name || 'Unknown Caller';
            console.log('✅ Caller name set:', leadData.cn_name);
        } else {
            console.error('❌ callerName element not found!');
        }
        
        if (callerPhoneEl) {
            callerPhoneEl.textContent = leadData.phone_number || 'No Number';
            console.log('✅ Caller phone set:', leadData.phone_number);
        } else {
            console.error('❌ callerPhone element not found!');
        }

        // Helper to format date for display
        const formatDateDisplay = (dateStr) => {
            if (!dateStr) return 'Not available';
            try {
                const date = new Date(dateStr);
                return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
            } catch {
                return dateStr;
            }
        };

        // Helper to format date for input (YYYY-MM-DD)
        const formatDateInput = (dateStr) => {
            if (!dateStr) return '';
            try {
                // If already in YYYY-MM-DD format, return as-is
                if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) return dateStr;
                // Otherwise parse and format
                const date = new Date(dateStr);
                return date.toISOString().split('T')[0];
            } catch {
                return '';
            }
        };

        // PHASE 2: Populate CURRENT VALUE displays (read-only)
        // Use safe element access to prevent crashes if elements don't exist
        const safeSetText = (id, value) => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = value;
            } else {
                console.warn(`⚠️ Element not found: ${id}`);
            }
        };
        
        const safeSetValue = (id, value) => {
            const element = document.getElementById(id);
            if (element) {
                element.value = value;
            } else {
                console.warn(`⚠️ Element not found: ${id}`);
            }
        };
        
        safeSetText('displayName', leadData.cn_name || 'Not available');
        safeSetText('displayPhone', leadData.phone_number || 'Not available');
        safeSetText('displayDOB', formatDateDisplay(leadData.date_of_birth));
        safeSetText('displaySSN', leadData.ssn || 'Not available');
        // Address fallback: use address, else state, else birth place
        let addressDisplay = leadData.address;
        if (!addressDisplay || addressDisplay.trim() === '') {
            addressDisplay = leadData.state || leadData.birth_place || 'Not available';
        }
        safeSetText('displayAddress', addressDisplay);
        
        // Handle beneficiaries display - show as comma-separated list
        // Show all current beneficiaries (names and DOBs if available)
        let beneficiariesDisplay = 'Not available';
        if (leadData.beneficiaries && leadData.beneficiaries.length > 0) {
            beneficiariesDisplay = leadData.beneficiaries.map(b => {
                if (b.dob) {
                    return b.name + ' (' + formatDateDisplay(b.dob) + ')';
                }
                return b.name;
            }).join(', ');
        }
        safeSetText('displayBeneficiary', beneficiariesDisplay);
        
        safeSetText('displayCarrier', leadData.carrier_name || 'Not available');
        safeSetText('displayCoverage', leadData.coverage_amount ? '$' + parseFloat(leadData.coverage_amount).toLocaleString() : 'Not available');
        safeSetText('displayPremium', leadData.monthly_premium ? '$' + parseFloat(leadData.monthly_premium).toFixed(2) : 'Not available');
        safeSetText('displayAccountNumber', leadData.account_number || 'Not available');

        // PHASE 2: Pre-fill CHANGES fields with existing values (user can modify)
        safeSetValue('phase2_name', '');
        safeSetValue('phase2_phone', '');
        safeSetValue('phase2_dob', formatDateInput(leadData.date_of_birth));
        safeSetValue('phase2_ssn', leadData.ssn || '');
        // Pre-fill address field with fallback
        let addressValue = leadData.address;
        if (!addressValue || addressValue.trim() === '') {
            addressValue = leadData.state || leadData.birth_place || '';
        }
        safeSetValue('phase2_address', addressValue);
        
        // Clear existing beneficiary rows
        const beneficiaryContainer = document.getElementById('beneficiaries-container-ravens');
        if (beneficiaryContainer) {
            beneficiaryContainer.innerHTML = '';
            window.beneficiaryIndexRavens = 0;
            
            // Populate beneficiaries from lead data
            if (leadData.beneficiaries && leadData.beneficiaries.length > 0) {
                leadData.beneficiaries.forEach((beneficiary, index) => {
                const row = document.createElement('div');
                row.className = 'beneficiary-ravens-row row mb-2';
                row.innerHTML = `
                    <div class="col-md-5">
                        <input type="text" class="form-control beneficiary-name-ravens" 
                               placeholder="Beneficiary Name" value="${beneficiary.name || ''}" required>
                    </div>
                    <div class="col-md-5">
                        <input type="date" class="form-control beneficiary-dob-ravens" 
                               value="${formatDateInput(beneficiary.dob || '')}" required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm remove-beneficiary-ravens">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>
                `;
                beneficiaryContainer.appendChild(row);
                window.beneficiaryIndexRavens++;
            });
            } else {
                // Add one empty beneficiary row
                const row = document.createElement('div');
                row.className = 'beneficiary-ravens-row row mb-2';
                row.innerHTML = `
                    <div class="col-md-5">
                        <input type="text" class="form-control beneficiary-name-ravens" 
                               placeholder="Beneficiary Name" required>
                    </div>
                    <div class="col-md-5">
                        <input type="date" class="form-control beneficiary-dob-ravens" required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm remove-beneficiary-ravens">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>
                `;
                beneficiaryContainer.appendChild(row);
                window.beneficiaryIndexRavens++;
            }
        } else {
            console.warn('⚠️ beneficiaries-container-ravens element not found');
        }
        
        safeSetValue('phase2_carrier', leadData.carrier_name || '');
        safeSetValue('phase2_coverage', leadData.coverage_amount || '');
        safeSetValue('phase2_premium', leadData.monthly_premium || '');
        safeSetValue('phase2_account_number', '');

        // Validate Phase 2 fields after populating
        if (typeof validatePhase2Fields === 'function') {
            validatePhase2Fields();
        }

        // Show modal and start at Phase 1
        const modalElement = document.getElementById('callDetailsModal');
        console.log('🎭 Modal element found:', modalElement);
        
        if (!modalElement) {
            console.error('❌ Modal element not found! Cannot show Ravens form.');
            return;
        }
        
        try {
            console.log('🔍 Checking Bootstrap availability:', typeof bootstrap);
            if (typeof bootstrap === 'undefined') {
                console.error('❌ Bootstrap is not available! This is the problem.');
                throw new Error('Bootstrap not available');
            }
            
            const modal = new bootstrap.Modal(modalElement);
            console.log('🎭 Bootstrap modal created:', modal);
            
            // CRITICAL: Show the modal first, then make sure phase1 is visible
            modal.show();
            console.log('✅ Modal.show() called');
            
            // Ensure phase1 is visible after modal shows
            setTimeout(() => {
                console.log('🎭 Making sure phase1 is visible...');
                goToPhase1();
                
                // Double-check phase1 visibility
                const phase1 = document.getElementById('phase1');
                if (phase1) {
                    console.log('🔍 Phase1 display style:', phase1.style.display);
                    if (phase1.style.display === 'none' || phase1.style.display === '') {
                        phase1.style.display = 'block';
                        console.log('🔧 Phase1 forced to display: block');
                    }
                }
            }, 100);
            
            console.log('✅ Ravens modal should now be visible with phase1');
            
            // Check if modal is actually visible after a longer delay to allow animation
            setTimeout(() => {
                const isVisible = modalElement.classList.contains('show');
                const computedStyle = window.getComputedStyle(modalElement);
                console.log('🔍 Modal visibility check:');
                console.log('  - Has "show" class:', isVisible);
                console.log('  - Display style:', computedStyle.display);
                console.log('  - Visibility style:', computedStyle.visibility);
                console.log('  - Opacity style:', computedStyle.opacity);
                console.log('  - Z-index:', computedStyle.zIndex);
                
                // Check if modal backdrop exists
                const backdrop = document.querySelector('.modal-backdrop');
                console.log('  - Backdrop exists:', !!backdrop);
                
                // Check phase1 visibility specifically
                const phase1 = document.getElementById('phase1');
                if (phase1) {
                    const phase1Style = window.getComputedStyle(phase1);
                    console.log('  - Phase1 display:', phase1Style.display);
                    console.log('  - Phase1 visibility:', phase1Style.visibility);
                }
                
                if (!isVisible || computedStyle.display === 'none') {
                    console.error('❌ Modal is not visible! There may be a CSS or Bootstrap issue.');
                    
                    // Force show the modal using direct DOM manipulation
                    console.log('🔧 Attempting manual modal visibility fix...');
                    modalElement.style.display = 'block';
                    modalElement.classList.add('show');
                    modalElement.setAttribute('aria-hidden', 'false');
                    modalElement.setAttribute('aria-modal', 'true');
                    modalElement.setAttribute('role', 'dialog');
                    
                    // Ensure modal is above everything else
                    modalElement.style.zIndex = '9999';
                    
                    // Also make sure phase1 is visible
                    if (phase1) {
                        phase1.style.display = 'block';
                        console.log('🔧 Phase1 also forced visible');
                    }
                    
                    console.log('🔧 Manual fix applied, checking again...');
                    setTimeout(() => {
                        const newStyle = window.getComputedStyle(modalElement);
                        console.log('🔍 After manual fix - Display:', newStyle.display, 'Visibility:', newStyle.visibility);
                    }, 100);
                } else {
                    console.log('✅ Modal appears to be visible correctly');
                }
            }, 800);
            
        } catch (error) {
            console.error('❌ Error showing modal:', error);
            console.log('💡 Trying fallback method...');
            // Fallback: try using jQuery if Bootstrap modal fails
            if (typeof $ !== 'undefined') {
                $('#callDetailsModal').modal('show');
                console.log('✅ Fallback: jQuery modal shown');
                goToPhase1();
            } else {
                console.error('❌ Both Bootstrap and jQuery modal methods failed');
            }
        }

        // Mark as read
        if (callData.event_id && !callData.event_id.toString().startsWith('test-')) {
            fetch(`/api/call-events/${callData.event_id}/mark-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
        }
    }

    // Old polling disabled - using Zoom API polling instead
    // startCallPolling();

    // Test function to manually trigger Ravens call modal (accessible from sidebar)
    window.testRavensCallModal = function() {
        console.log('Ravens test button clicked');
        const testCallData = {
            event_id: 'test-' + Date.now(),
            lead_data: {
                id: 999999,
                cn_name: 'John Test Ravens Customer',
                phone_number: '+1-555-987-6543',
                date_of_birth: '1985-06-15',
                ssn: '123-45-6789',
                gender: 'Male',
                birth_place: 'Test City',
                smoker: 0,
                height_weight: '5ft 10in, 180 lbs',
                address: '123 Test Street, Test City, TX 12345',
                beneficiary: 'Jane Test Beneficiary',
                carrier_name: 'Test Insurance Co',
                coverage_amount: '100000',
                monthly_premium: '75.50',
                closer_name: <?php echo json_encode(Auth::user()->name ?? 'Test Closer', 15, 512) ?>,
                source: 'Test Source'
            },
            call_connected_at: new Date().toISOString()
        };
        
        console.log('Calling showCallModal with:', testCallData);
        showCallModal(testCallData);
        toastr.info('Test Ravens modal opened with sample data', 'Test Mode');
    }

    /**
     * Monitor call connection intelligently
     */
    function startCallConnectionMonitor(leadId, phoneNumber) {
        let checkAttempts = 0;
        const maxAttempts = 10; // Check for 20 seconds (2 sec intervals)
        
        const checkConnection = () => {
            checkAttempts++;
            
            // Ask user if call connected after reasonable time
            if (checkAttempts === 3) { // After 6 seconds
                const isConnected = confirm('Is your call connected? Click OK if the call connected successfully, or Cancel if not connected yet.');
                
                if (isConnected) {
                    triggerRavensFormForLead(leadId, phoneNumber);
                    return;
                }
            }
            
            // Auto-trigger after 15 seconds as fallback
            if (checkAttempts >= 8) {
                console.log('Auto-triggering Ravens form after 15 seconds');
                triggerRavensFormForLead(leadId, phoneNumber);
                return;
            }
            
            // Continue checking
            if (checkAttempts < maxAttempts) {
                setTimeout(checkConnection, 2000);
            }
        };
        
        // Start checking after 2 seconds
        setTimeout(checkConnection, 2000);
    }

    /**
     * Trigger Ravens form popup for a specific lead (manual trigger)
     */
    function triggerRavensFormForLead(leadId, phoneNumber) {
        // Fetch lead data first
        fetch(`/api/leads/${leadId}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(leadData => {
            if (leadData) {
                // Create call event data similar to webhook format
                const callEventData = {
                    event_id: 'manual-dial-' + Date.now(),
                    lead_data: leadData,
                    call_connected_at: new Date().toISOString(),
                    caller_number: phoneNumber,
                    callee_number: phoneNumber
                };
                
                console.log('Triggering Ravens form for lead:', leadData.cn_name || leadData.name || 'Unknown');
                showCallModal(callEventData);
                toastr.success('Call connected - Ravens form opened', 'Call Connected');
            }
        })
        .catch(error => {
            console.error('Failed to fetch lead data:', error);
            toastr.error('Could not load lead information');
        });
    }
    
    /**
     * Save and Exit - Save lead data without marking as sale
     */
    function saveAndExit() {
        // Collect all form data from Phase 3
        const leadId = window.currentLeadData?.id;
        
        if (!leadId) {
            toastr.error('Lead ID not found');
            return;
        }
        
        const formData = {
            lead_id: leadId,
            cn_name: document.getElementById('change_name')?.value || null,
            phone_number: document.getElementById('change_phone')?.value || null,
            date_of_birth: document.getElementById('change_dob')?.value || null,
            ssn: document.getElementById('change_ssn')?.value || null,
            gender: document.getElementById('change_gender')?.value || null,
            address: document.getElementById('change_address')?.value || null,
            beneficiary: document.getElementById('change_beneficiary')?.value || null,
            beneficiary_dob: document.getElementById('change_beneficiary_dob')?.value || null,
            policy_type: document.getElementById('change_policy_type')?.value || null,
            carrier_name: document.getElementById('change_carrier')?.value || null,
            coverage_amount: document.getElementById('change_coverage')?.value || null,
            monthly_premium: document.getElementById('change_premium')?.value || null,
            initial_draft_date: document.getElementById('change_draft_date')?.value || null,
            bank_name: document.getElementById('change_bank_name')?.value || null,
            account_type: document.getElementById('change_account_type')?.value || null,
            routing_number: document.getElementById('change_routing')?.value || null,
            account_number: document.getElementById('change_account')?.value || null,
            account_verified_by: document.getElementById('change_verified_by')?.value || null,
            bank_balance: document.getElementById('change_balance')?.value || null,
            source: document.getElementById('change_source')?.value || null,
            closer_name: document.getElementById('change_closer')?.value || null,
        };
        
        // Send to server
        fetch('/ravens/leads/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success('Lead information saved successfully');
                // Close modal
                closeCallModal();
            } else {
                toastr.error(data.message || 'Failed to save lead information');
            }
        })
        .catch(error => {
            console.error('Error saving lead:', error);
            toastr.error('An error occurred while saving');
        });
    }
    
    /**
     * Submit Sale - Mark lead as sold and send to sales section
     */
    function submitSale() {
        const leadId = window.currentLeadData?.id;
        
        if (!leadId) {
            toastr.error('Lead ID not found');
            return;
        }
        
        // All fields are now optional, skip required field validation
        
        // Collect beneficiary data
        const beneficiaries = [];
        document.querySelectorAll('.beneficiary-ravens-row').forEach((row, index) => {
            const name = row.querySelector(`[name="beneficiaries[${index}][name]"]`)?.value;
            const dob = row.querySelector(`[name="beneficiaries[${index}][dob]"]`)?.value;
            if (name) {
                beneficiaries.push({ name: name, dob: dob || null });
            }
        });
        
        // Collect all form data
        const formData = {
            lead_id: leadId,
            cn_name: document.getElementById('phase2_name')?.value || document.getElementById('change_name')?.value || null,
            phone_number: document.getElementById('phase2_phone')?.value || document.getElementById('change_phone')?.value || null,
            date_of_birth: document.getElementById('phase2_dob')?.value || document.getElementById('change_dob')?.value || null,
            ssn: document.getElementById('phase2_ssn')?.value || document.getElementById('change_ssn')?.value || null,
            gender: document.getElementById('phase2_gender')?.value || document.getElementById('change_gender')?.value || null,
            address: document.getElementById('phase2_address')?.value || document.getElementById('change_address')?.value || null,
            beneficiaries: beneficiaries,
            policy_type: document.getElementById('change_policy_type')?.value || null,
            carrier_name: document.getElementById('phase2_carrier')?.value || document.getElementById('change_carrier')?.value || null,
            coverage_amount: document.getElementById('phase2_coverage')?.value || document.getElementById('change_coverage')?.value || null,
            monthly_premium: document.getElementById('phase2_premium')?.value || document.getElementById('change_premium')?.value || null,
            initial_draft_date: document.getElementById('change_draft_date')?.value || null,
            bank_name: document.getElementById('change_bank_name')?.value || null,
            account_type: document.getElementById('change_account_type')?.value || null,
            routing_number: document.getElementById('change_routing')?.value || null,
            account_number: document.getElementById('phase2_account_number')?.value || document.getElementById('change_account')?.value || null,
            account_verified_by: document.getElementById('change_verified_by')?.value || null,
            bank_balance: document.getElementById('change_balance')?.value || null,
            source: document.getElementById('change_source')?.value || null,
            closer_name: <?php echo json_encode(Auth::user()->name ?? 'Unknown', 15, 512) ?>,
            policy_carrier: document.getElementById('phase3_policy_carrier')?.value || null,
            state: document.getElementById('phase3_approved_state')?.value || null,
        };
        
        // Confirm submission
        if (!confirm('Are you sure you want to submit this sale? This will move the lead to the sales section and notify QA and managers.')) {
            return;
        }
        
        // Send to server
        fetch('/ravens/leads/submit-sale', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success(data.message || 'Sale submitted successfully!');
                
                // Show warning if this is a repeat sale
                if (data.is_repeat_sale) {
                    toastr.warning(data.repeat_sale_message, 'Repeat Sale Detected', {
                        timeOut: 10000
                    });
                }
                
                // Close modal
                closeCallModal();
                
                // Reload page to refresh leads list
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                toastr.error(data.message || 'Failed to submit sale');
            }
        })
        .catch(error => {
            console.error('Error submitting sale:', error);
            toastr.error('An error occurred while submitting sale');
        });
    }
    
    // Professional Zoom API Integration ✅
    // - Uses real OAuth authentication with Zoom
    // - Professional call status monitoring via Zoom API
    // - Ravens form appears only when call is verified as completed
    // - No popups or confirmations - direct professional calling
    
    // Beneficiary management for Ravens form
    document.addEventListener('DOMContentLoaded', function() {
        let beneficiaryIndexRavens = 1;
        
        const addBeneficiaryBtn = document.getElementById('add-beneficiary-ravens');
        if (addBeneficiaryBtn) {
            addBeneficiaryBtn.addEventListener('click', function() {
                const container = document.getElementById('beneficiaries-ravens-container');
                const newRow = document.createElement('div');
                newRow.className = 'row g-2 mb-2 beneficiary-ravens-row';
                newRow.setAttribute('data-index', beneficiaryIndexRavens);
                newRow.innerHTML = `
                    <div class="col-md-6">
                        <input type="text" class="form-control form-control-sm" name="beneficiaries[${beneficiaryIndexRavens}][name]" placeholder="Beneficiary Name ${beneficiaryIndexRavens + 1}">
                    </div>
                    <div class="col-md-4">
                        <input type="date" class="form-control form-control-sm" name="beneficiaries[${beneficiaryIndexRavens}][dob]" placeholder="DOB">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm w-100 remove-beneficiary-ravens">
                            <i class="bx bx-minus"></i>
                        </button>
                    </div>
                `;
                container.appendChild(newRow);
                beneficiaryIndexRavens++;
                
                // Attach remove handler
                newRow.querySelector('.remove-beneficiary-ravens').addEventListener('click', function() {
                    newRow.remove();
                });
            });
        }
        
        // Remove beneficiary (for existing rows)
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-beneficiary-ravens')) {
                e.target.closest('.beneficiary-ravens-row').remove();
            }
        });
    });

    /**
     * Dispose current lead with a disposition reason
     */
    function disposeCurrentLead(disposition) {
        if (!window.currentLeadData || !window.currentLeadData.id) {
            toastr.error('No active lead to dispose');
            return;
        }

        const dispositionLabels = {
            'no_answer': 'No Answer',
            'wrong_number': 'Wrong Number',
            'wrong_details': 'Wrong Details'
        };

        const confirmMessage = `Are you sure you want to dispose this lead as "${dispositionLabels[disposition]}"?`;
        
        if (!confirm(confirmMessage)) {
            return;
        }

        // Optional: Ask for notes
        const notes = prompt('Add notes (optional):');

        // Send disposition request
        fetch('<?php echo e(route('ravens.leads.dispose')); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                lead_id: window.currentLeadData.id,
                disposition: disposition,
                notes: notes || ''
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success('Lead disposed successfully as ' + data.disposition);
                
                // Close modal and remove from list
                $('#callingModal').modal('hide');
                window.currentLeadData = null;
                window.isCallActive = false;
                
                // Reload page to refresh lead list
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                toastr.error(data.message || 'Failed to dispose lead');
            }
        })
        .catch(error => {
            console.error('Error disposing lead:', error);
            toastr.error('An error occurred while disposing the lead');
        });
    }
    
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/ravens/calling.blade.php ENDPATH**/ ?>