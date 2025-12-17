@extends('layouts.master')

@section('title')
    Ravens Calling System
@endsection

@section('css')
    <link href="{{ URL::asset('public/css/light-theme.css') }}" rel="stylesheet" type="text/css" />
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
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Ravens
        @endslot
        @slot('title')
            Ravens Calling System
        @endslot
    @endcomponent

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
                                @forelse($leads as $index => $lead)
                                    <tr class="lead-row" data-lead-id="{{ $lead->id }}" data-phone="{{ $lead->phone_number }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $lead->cn_name ?? 'N/A' }}</strong>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-primary btn-sm dial-btn" onclick="dialLead('{{ $lead->id }}', '{{ $lead->phone_number }}', this)">
                                                <i class="bx bx-phone-call"></i> Dial
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            <i class="bx bx-info-circle fs-3"></i>
                                            <p class="mb-0">No leads available</p>
                                        </td>
                                    </tr>
                                @endforelse
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
                            <i class="fas fa-info-circle me-2"></i> Fill all required fields to unlock detailed information
                        </div>

                        <div class="row g-3">
                            <!-- Name & Phone (Read-only display) -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Name:</label>
                                <div class="p-2 bg-light rounded" id="displayName"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Phone Number:</label>
                                <div class="p-2 bg-light rounded" id="displayPhone"></div>
                            </div>

                            <!-- DOB -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">DOB: <span class="text-danger">*</span></label>
                                <input type="date" class="form-control required-field" id="phase2_dob" required>
                            </div>

                            <!-- SSN -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">SSN: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control required-field" id="phase2_ssn" placeholder="XXX-XX-XXXX" required>
                            </div>

                            <!-- Beneficiary -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Beneficiary: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control required-field" id="phase2_beneficiary" required>
                            </div>

                            <!-- Carrier -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Carrier: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control required-field" id="phase2_carrier" required>
                            </div>

                            <!-- Coverage Amount -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Coverage Amount: <span class="text-danger">*</span></label>
                                <input type="number" class="form-control required-field" id="phase2_coverage" step="0.01" required>
                            </div>

                            <!-- Premium -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Premium: <span class="text-danger">*</span></label>
                                <input type="number" class="form-control required-field" id="phase2_premium" step="0.01" required>
                            </div>
                        </div>

                        <!-- Assignment Section -->
                        <div class="alert alert-warning mt-3 mb-3">
                            <strong><i class="fas fa-user-tag me-2"></i>Sale Assignment</strong> - Select policy carrier, partner/agent, and approved states
                        </div>
                        <div class="row g-3">
                            <!-- Policy Carrier -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Policy Carrier: <span class="text-danger">*</span></label>
                                <select class="form-select required-field" id="phase2_policy_carrier" required>
                                    <option value="">Select Carrier</option>
                                    <option value="AMAM">AMAM</option>
                                    <option value="Combined">Combined</option>
                                    <option value="AIG">AIG</option>
                                    <option value="LBL">LBL</option>
                                </select>
                            </div>

                            <!-- Partner/Agent -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Partner/Agent: <span class="text-danger">*</span></label>
                                <select class="form-select required-field" id="phase2_partner_agent" required>
                                    <option value="">Select Partner/Agent</option>
                                    <option value="partner_1">John Partner</option>
                                    <option value="agent_1">-- Agent Mike</option>
                                    <option value="agent_2">-- Agent Sarah</option>
                                    <option value="partner_2">Jane Partner</option>
                                    <option value="agent_3">-- Agent Tom</option>
                                </select>
                            </div>

                            <!-- States -->
                            <div class="col-md-4">
                                <label class="form-label fw-bold">State: <span class="text-danger">*</span></label>
                                <select class="form-select required-field" id="phase2_approved_state" required>
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
                            <button type="button" class="btn btn-secondary" onclick="goToPhase1()">
                                <i class="fas fa-arrow-left me-2"></i> Back
                            </button>
                            <button type="button" class="btn btn-lg" style="background: linear-gradient(135deg, #d4af37 0%, #b8941f 100%); color: white;" id="showMoreBtn" disabled onclick="goToPhase3()">
                                <i class="fas fa-unlock me-2"></i> Show More Details
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
                        </div>

                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-secondary" onclick="goToPhase2()">
                                <i class="fas fa-arrow-left me-2"></i> Back to Essential Fields
                            </button>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-phone-slash me-1"></i> End Call</button>
                    <button type="button" class="btn btn-success"><i class="fas fa-save me-1"></i> Save & Exit</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.0/dist/echo.iife.js"></script>
<script>
    let autoDialActive = false;
    let currentLeadIndex = 0;
    let dialedLeads = new Set();
    let isCallActive = false;
    let autoDialTimeout = null;

    // Get user's zoom number
    window.zoomNumber = '{{ Auth::user()->zoom_number ?? '' }}';
    window.sanitizedZoomNumber = '{{ Auth::user()->sanitized_zoom_number ?? '' }}';

    // Setup Echo for call status monitoring
    @if(Auth::user()->zoom_number)
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: '{{ env('PUSHER_APP_KEY') }}',
        cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
        forceTLS: true,
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }
    });

    // Listen for call status changes
    window.Echo.private('calls.' + window.sanitizedZoomNumber)
        .listen('CallStatusChanged', (e) => {
            console.log('Call status:', e.status);

            if (e.status === 'connected') {
                isCallActive = true;
                console.log('Call connected - Auto-dial paused');
            } else if (e.status === 'disconnected' || e.status === 'ended') {
                isCallActive = false;
                console.log('Call ended - Resuming auto-dial in 2 seconds');

                // Mark current lead as dialed
                if (currentLeadIndex >= 0) {
                    const rows = document.querySelectorAll('.lead-row');
                    if (rows[currentLeadIndex]) {
                        const leadId = rows[currentLeadIndex].dataset.leadId;
                        dialedLeads.add(leadId);
                        rows[currentLeadIndex].classList.remove('calling');
                        rows[currentLeadIndex].classList.add('dialed');
                    }
                }

                // Resume auto-dial after 2 seconds if active
                if (autoDialActive) {
                    setTimeout(() => {
                        currentLeadIndex++;
                        autoDialNext();
                    }, 2000);
                }
            }
        });
    @endif

    // Auto-dial toggle button
    document.getElementById('autoDialBtn').addEventListener('click', function() {
        autoDialActive = !autoDialActive;
        const btn = this;
        const text = document.getElementById('autoDialText');
        const icon = btn.querySelector('i');

        if (autoDialActive) {
            btn.classList.add('active');
            text.textContent = 'Stop Auto-Dial';
            icon.className = 'bx bx-stop-circle me-1';
            currentLeadIndex = 0;
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
        if (!autoDialActive) return;
        if (isCallActive) {
            console.log('Call in progress, waiting...');
            return;
        }

        const rows = document.querySelectorAll('.lead-row');

        // Find next undailed lead
        while (currentLeadIndex < rows.length) {
            const row = rows[currentLeadIndex];
            const leadId = row.dataset.leadId;

            if (!dialedLeads.has(leadId)) {
                // Found undailed lead, dial it
                const phone = row.dataset.phone;
                const dialBtn = row.querySelector('.dial-btn');

                // Highlight current row
                document.querySelectorAll('.lead-row').forEach(r => r.classList.remove('calling'));
                row.classList.add('calling');

                // Scroll to current lead
                row.scrollIntoView({ behavior: 'smooth', block: 'center' });

                // Dial the lead
                dialLead(leadId, phone, dialBtn);
                return;
            }

            currentLeadIndex++;
        }

        // All leads dialed
        autoDialActive = false;
        const btn = document.getElementById('autoDialBtn');
        const text = document.getElementById('autoDialText');
        const icon = btn.querySelector('i');
        btn.classList.remove('active');
        text.textContent = 'Start Auto-Dial';
        icon.className = 'bx bx-play-circle me-1';

        alert('All leads have been dialed!');
    }

    // Manual dial function
    function dialLead(leadId, phoneNumber, button) {
        if (!phoneNumber) {
            alert('No phone number available for this lead');
            return;
        }

        // Track this call
        fetch('/api/call-logs', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                lead_id: leadId,
                phone_number: phoneNumber,
                status: 'initiated'
            })
        });

        // Clean phone number and create Zoom URL
        const cleanPhone = phoneNumber.replace(/[^\d\+]/g, '');
        const zoomUrl = 'zoomphonecall://' + encodeURIComponent(cleanPhone);

        // Open Zoom Phone
        window.location.href = zoomUrl;

        // Mark lead as dialed
        dialedLeads.add(leadId);

        // Update UI
        if (!autoDialActive) {
            const row = button.closest('.lead-row');
            row.classList.add('calling');
            setTimeout(() => {
                row.classList.remove('calling');
                row.classList.add('dialed');
            }, 1000);
        }
    }

    // ===== LOCAL POLLING SYSTEM FOR CALL POPUP =====
    let currentEventId = null;
    let pollInterval = null;

    function startCallPolling() {
        console.log('Starting call event polling...');
        pollInterval = setInterval(checkForCallEvents, 2000);
        checkForCallEvents(); // Check immediately
    }

    function checkForCallEvents() {
        fetch('/api/call-events/poll', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.has_call && data.event_id !== currentEventId) {
                currentEventId = data.event_id;
                showCallModal(data);
            }
        })
        .catch(error => console.error('Polling error:', error));
    }

    // ===== PHASE NAVIGATION SYSTEM =====
    let currentLeadData = null;

    function goToPhase1() {
        document.getElementById('phase1').style.display = 'block';
        document.getElementById('phase2').style.display = 'none';
        document.getElementById('phase3').style.display = 'none';
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
        const ld = currentLeadData;

        // Personal Information
        document.getElementById('orig_name').textContent = ld.cn_name || 'N/A';
        document.getElementById('orig_phone').textContent = ld.phone_number || 'N/A';
        document.getElementById('orig_dob').textContent = ld.date_of_birth || 'N/A';
        document.getElementById('orig_gender').textContent = ld.gender || 'N/A';
        document.getElementById('orig_birthplace').textContent = ld.birth_place || 'N/A';
        document.getElementById('orig_ssn').textContent = document.getElementById('phase2_ssn').value || 'N/A';
        document.getElementById('orig_smoker').textContent = ld.smoker == 1 ? 'Yes' : 'No';
        document.getElementById('orig_height_weight').textContent = ld.height_weight || 'N/A';
        document.getElementById('orig_address').textContent = ld.address || 'N/A';

        // Medical Information
        document.getElementById('orig_medical_issue').textContent = ld.medical_issue || 'N/A';
        document.getElementById('orig_medications').textContent = ld.medications || 'N/A';
        document.getElementById('orig_doctor').textContent = ld.doctor_name || 'N/A';
        document.getElementById('orig_doctor_address').textContent = ld.doctor_address || 'N/A';

        // Policy Information
        document.getElementById('orig_beneficiary').textContent = document.getElementById('phase2_beneficiary').value || 'N/A';
        document.getElementById('orig_beneficiary_dob').textContent = ld.beneficiary_dob || 'N/A';
        document.getElementById('orig_policy_type').textContent = ld.policy_type || 'N/A';
        document.getElementById('orig_carrier').textContent = document.getElementById('phase2_carrier').value || 'N/A';
        document.getElementById('orig_coverage').textContent = document.getElementById('phase2_coverage').value || 'N/A';
        document.getElementById('orig_premium').textContent = document.getElementById('phase2_premium').value || 'N/A';
        document.getElementById('orig_draft_date').textContent = ld.initial_draft_date || 'N/A';

        // Banking Information
        document.getElementById('orig_bank_name').textContent = ld.bank_name || 'N/A';
        document.getElementById('orig_account_type').textContent = ld.account_type || 'N/A';
        document.getElementById('orig_routing').textContent = ld.routing_number || 'N/A';
        document.getElementById('orig_account').textContent = ld.account_number || 'N/A';
        document.getElementById('orig_verified_by').textContent = ld.verified_by || 'N/A';
        document.getElementById('orig_balance').textContent = ld.bank_balance || 'N/A';

        // Additional Information
        document.getElementById('orig_source').textContent = ld.source || 'N/A';
        document.getElementById('orig_closer').textContent = ld.closer_name || 'N/A';

        // Pre-fill change inputs with Phase 2 data
        document.getElementById('change_name').value = ld.cn_name || '';
        document.getElementById('change_phone').value = ld.phone_number || '';
        document.getElementById('change_dob').value = document.getElementById('phase2_dob').value || '';
        document.getElementById('change_ssn').value = document.getElementById('phase2_ssn').value || '';
        document.getElementById('change_beneficiary').value = document.getElementById('phase2_beneficiary').value || '';
        document.getElementById('change_carrier').value = document.getElementById('phase2_carrier').value || '';
        document.getElementById('change_coverage').value = document.getElementById('phase2_coverage').value || '';
        document.getElementById('change_premium').value = document.getElementById('phase2_premium').value || '';
    }

    function validatePhase2Fields() {
        const requiredFields = document.querySelectorAll('#phase2 .required-field');
        let allFilled = true;

        requiredFields.forEach(field => {
            if (!field.value || field.value.trim() === '') {
                allFilled = false;
            }
        });

        const showMoreBtn = document.getElementById('showMoreBtn');
        if (allFilled) {
            showMoreBtn.disabled = false;
            showMoreBtn.classList.remove('btn-secondary');
        } else {
            showMoreBtn.disabled = true;
            showMoreBtn.classList.add('btn-secondary');
        }
    }

    // Add event listeners to Phase 2 required fields
    document.addEventListener('DOMContentLoaded', function() {
        const requiredFields = document.querySelectorAll('#phase2 .required-field');
        requiredFields.forEach(field => {
            field.addEventListener('input', validatePhase2Fields);
            field.addEventListener('change', validatePhase2Fields);
        });
    });

    function showCallModal(callData) {
        console.log('=== CALL CONNECTED ===', callData);
        const leadData = callData.lead_data;
        currentLeadData = leadData;

        // PHASE 1: Show caller identification
        document.getElementById('callerName').textContent = leadData.cn_name || 'Unknown Caller';
        document.getElementById('callerPhone').textContent = leadData.phone_number || 'No Number';

        // PHASE 2: Populate display and pre-fill fields
        document.getElementById('displayName').textContent = leadData.cn_name || 'N/A';
        document.getElementById('displayPhone').textContent = leadData.phone_number || 'N/A';
        document.getElementById('phase2_dob').value = leadData.date_of_birth || '';
        document.getElementById('phase2_ssn').value = leadData.ssn || '';
        document.getElementById('phase2_beneficiary').value = leadData.beneficiary || '';
        document.getElementById('phase2_carrier').value = leadData.carrier_name || '';
        document.getElementById('phase2_coverage').value = leadData.coverage_amount || '';
        document.getElementById('phase2_premium').value = leadData.monthly_premium || '';

        // Validate Phase 2 fields after populating
        validatePhase2Fields();

        // Show modal and start at Phase 1
        const modalElement = document.getElementById('callDetailsModal');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        goToPhase1();

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

    // Start polling
    startCallPolling();

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
                closer_name: @json(Auth::user()->name ?? 'Test Closer'),
                source: 'Test Source'
            },
            call_connected_at: new Date().toISOString()
        };
        
        console.log('Calling showCallModal with:', testCallData);
        showCallModal(testCallData);
        toastr.info('Test Ravens modal opened with sample data', 'Test Mode');
    }
</script>
@endsection
