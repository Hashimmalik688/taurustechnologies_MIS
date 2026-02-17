@extends('layouts.master')

@section('title')
    Ravens Calling System
@endsection

@section('css')
    <link href="{{ URL::asset('build/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('build/libs/toastr/build/toastr.min.css') }}" />
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
        
        /* Per-user dial tracking badges */
        .dial-badges {
            display: flex;
            gap: 3px;
            flex-wrap: wrap;
            align-items: center;
        }
        .dial-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            font-size: 0.65rem;
            font-weight: 700;
            color: #fff;
            cursor: default;
            position: relative;
        }
        .dial-badge.is-mine {
            outline: 2px solid #000;
            outline-offset: 1px;
        }
        .dial-badge .dial-time {
            display: none;
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: #333;
            color: #fff;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.7rem;
            white-space: nowrap;
            z-index: 100;
        }
        .dial-badge:hover .dial-time {
            display: block;
        }
        /* Row highlighting for dialed-by-me leads */
        .lead-row.dialed-by-me {
            background-color: rgba(78, 115, 223, 0.06) !important;
            border-left: 3px solid #4e73df;
        }
        /* Row highlighting for dialed-by-others */
        .lead-row.dialed-by-others {
            background-color: rgba(231, 74, 59, 0.04) !important;
        }
        
        /* Peregrine badge style */
        .bg-purple {
            background-color: #6f42c1 !important;
            color: #fff !important;
        }
        
        /* Pagination - hide large icons and use text */
        .pagination .page-link svg {
            display: none !important;
        }
        .pagination .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }
        /* Add text content for Previous/Next */
        .pagination .page-item:first-child .page-link::before {
            content: "‹ Previous";
        }
        .pagination .page-item:last-child .page-link::before {
            content: "Next ›";
        }
        .pagination .page-item:first-child .page-link span,
        .pagination .page-item:last-child .page-link span {
            display: none;
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
            @php
                $hasZoomToken = \App\Models\ZoomToken::where('user_id', Auth::id())
                    ->where('expires_at', '>', now())
                    ->exists();
            @endphp
            @if(!$hasZoomToken)
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bx bx-phone-off me-2"></i>
                <strong>Zoom Phone Not Connected!</strong> You need to connect your Zoom Phone account to make calls.
                <a href="/zoom/authorize" class="btn btn-sm btn-primary ms-3">
                    <i class="bx bx-link-external me-1"></i> Connect Zoom Now
                </a>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            
            <div class="card bordered">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Leads to Call</h4>
                    <div class="d-flex gap-2">
                        <button id="autoDialBtn" class="btn btn-success auto-dial-btn">
                            <i class="bx bx-play-circle me-1"></i>
                            <span id="autoDialText">Start Auto-Dial</span>
                        </button>
                        <button onclick="testRavensFormOpen()" class="btn btn-info btn-sm">
                            <i class="bx bx-test-tube me-1"></i> Test Form
                        </button>
                        <button onclick="testZoomProtocol()" class="btn btn-warning btn-sm">
                            <i class="bx bx-test-tube me-1"></i> Test Zoom
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Dial Tracking Legend -->
                    <div class="d-flex align-items-center gap-3 mb-3 p-2 border rounded" style="background: #f8f9fa; font-size: 0.85rem;">
                        <strong><i class="bx bx-info-circle me-1"></i> Dial Tracking:</strong>
                        <span><span class="dial-badge is-mine" style="background-color: #4e73df; width: 20px; height: 20px; font-size: 0.55rem; display: inline-flex;">ME</span> = You dialed</span>
                        <span><span class="dial-badge" style="background-color: #e74a3b; width: 20px; height: 20px; font-size: 0.55rem; display: inline-flex;">AB</span> = Another closer dialed</span>
                        <span class="text-muted">| Hover badge to see name & time | Updates every 30s</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">#</th>
                                    <th>Customer Name</th>
                                    <th width="250">Callback Note <small class="text-muted">(auto-clears after 3 days)</small></th>
                                    <th width="100" class="text-center">Dialed By</th>
                                    <th width="120" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="leadsTableBody">
                                @forelse($leads as $index => $lead)
                                    <tr class="lead-row" data-lead-id="{{ $lead->id }}" data-phone="{{ $lead->phone_number }}" data-secondary-phone="{{ $lead->secondary_phone_number ?? '' }}">
                                        <td>{{ $leads->firstItem() + $index }}</td>
                                        <td>
                                            <strong>{{ $lead->cn_name ?? 'N/A' }}</strong>
                                            @if(
                                                ($lead->closer_name && isset($peregrineClosers) && in_array($lead->closer_name, $peregrineClosers)) ||
                                                (strtolower($lead->team ?? '') === 'peregrine') ||
                                                (stripos($lead->assigned_partner ?? '', 'peregrine') !== false)
                                            )
                                                <span class="badge bg-purple ms-1" title="Peregrine">Peregrine</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                // Auto-clear callback note if older than 3 days
                                                $showNote = false;
                                                $noteValue = '';
                                                if ($lead->callback_note && $lead->callback_note_updated_at) {
                                                    $noteAge = $lead->callback_note_updated_at->diffInDays(now(), false);
                                                    if ($noteAge < 3) {
                                                        $showNote = true;
                                                        $noteValue = $lead->callback_note;
                                                    }
                                                }
                                            @endphp
                                            <input 
                                                type="text" 
                                                class="form-control form-control-sm callback-note-input" 
                                                data-lead-id="{{ $lead->id }}"
                                                value="{{ $noteValue }}"
                                                placeholder="e.g., John's callback - 2pm"
                                                onblur="saveCallbackNote({{ $lead->id }}, this.value)"
                                                style="font-size: 0.85rem;">
                                            @if($showNote && $lead->callback_note_updated_at)
                                                <small class="text-muted d-block mt-1">
                                                    <i class="bx bx-time-five"></i> {{ $lead->callback_note_updated_at->diffForHumans() }}
                                                </small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="dial-badges" id="dial-badges-{{ $lead->id }}">
                                                <!-- Populated by JS from server -->
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-primary btn-sm dial-btn" onclick="makeCall('{{ $lead->id }}', '{{ $lead->phone_number }}', this)">
                                                <i class="bx bx-phone-call"></i> Call
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="bx bx-info-circle fs-3"></i>
                                            <p class="mb-0">No leads available</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($leads->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3 px-2">
                        <small class="text-muted">Showing {{ $leads->firstItem() }}–{{ $leads->lastItem() }} of {{ $leads->total() }} leads</small>
                        {{ $leads->links() }}
                    </div>
                    @endif
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
                                <p class="mb-1"><span class="badge bg-info">Current</span> <span class="text-primary fw-bold" id="displayName">-</span></p>
                                <label class="form-label small">Enter new name if changed</label>
                                <input type="text" class="form-control" id="phase2_name" placeholder="Leave empty if no change">
                            </div>

                            <!-- Phone Number -->
                            <div class="col-md-6">
                                <p class="mb-1"><span class="badge bg-info">Current</span> <span class="text-primary fw-bold" id="displayPhone">-</span></p>
                                <label class="form-label small">Enter new phone if changed</label>
                                <input type="text" class="form-control" id="phase2_phone" placeholder="Leave empty if no change">
                            </div>

                            <!-- Secondary Phone Number -->
                            <div class="col-md-6">
                                <p class="mb-1"><span class="badge bg-info">Current</span> <span class="text-primary fw-bold" id="displaySecondaryPhone">-</span></p>
                                <label class="form-label small">Secondary Phone</label>
                                <input type="text" class="form-control" id="phase2_secondary_phone" placeholder="Leave empty if no change">
                            </div>

                            <!-- State -->
                            <div class="col-md-6">
                                <p class="mb-1"><span class="badge bg-info">Current</span> <span class="text-primary fw-bold" id="displayState">-</span></p>
                                <label class="form-label small">State</label>
                                <select class="form-select" id="phase2_state">
                                    <option value="">Select State</option>
                                    @foreach($usStates as $code => $name)
                                        <option value="{{ $code }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Zip Code -->
                            <div class="col-md-6">
                                <p class="mb-1"><span class="badge bg-info">Current</span> <span class="text-primary fw-bold" id="displayZipCode">-</span></p>
                                <label class="form-label small">Zip Code</label>
                                <input type="text" class="form-control" id="phase2_zip" placeholder="Leave empty if no change">
                            </div>

                            <!-- DOB -->
                            <div class="col-md-6">
                                <p class="mb-1"><span class="badge bg-info">Current</span> <span class="text-primary fw-bold" id="displayDOB">-</span></p>
                                <label class="form-label small">Date of Birth</label>
                                <input type="date" class="form-control" id="phase2_dob">
                            </div>
                            
                            <!-- SSN -->
                            <div class="col-md-6">
                                <p class="mb-1"><span class="badge bg-info">Current</span> <span class="text-primary fw-bold" id="displaySSN">-</span></p>
                                <label class="form-label small">SSN</label>
                                <input type="text" class="form-control" id="phase2_ssn" placeholder="XXX-XX-XXXX">
                            </div>

                            <!-- Address -->
                            <div class="col-12">
                                <p class="mb-1"><span class="badge bg-info">Current</span> <span class="text-primary fw-bold" id="displayAddress">-</span></p>
                                <label class="form-label small">Address</label>
                                <input type="text" class="form-control" id="phase2_address" placeholder="Enter address">
                            </div>

                            <!-- Emergency Contact -->
                            <div class="col-12">
                                <p class="mb-1"><span class="badge bg-info">Current</span> <span class="text-primary fw-bold" id="displayEmergencyContact">-</span></p>
                                <label class="form-label small">Emergency Contact</label>
                                <input type="text" class="form-control" id="phase2_emergency_contact" placeholder="Leave empty if no change">
                            </div>

                            <!-- Beneficiary -->
                            <div class="col-12">
                                <p class="mb-1"><span class="badge bg-info">Current</span> <span class="text-success fw-bold" id="displayBeneficiary">-</span></p>
                                <label class="form-label small">Add/Update Beneficiaries</label>
                                <div id="beneficiaries-container-ravens">
                                    <!-- Beneficiaries will be populated dynamically -->
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-success mt-2" onclick="window.addBeneficiaryRow()">
                                    <i class="bx bx-plus"></i> Add Beneficiary
                                </button>
                            </div>

                            <!-- Policy Carrier -->
                            <div class="col-md-4">
                                <p class="mb-1"><span class="badge bg-info">Current</span> <span class="text-primary fw-bold" id="displayCarrier">-</span></p>
                                <label class="form-label small">Policy Carrier</label>
                                <select class="form-select" id="phase2_carrier">
                                    <option value="">Select Carrier</option>
                                    @foreach($insuranceCarriers as $carrier)
                                        <option value="{{ $carrier }}">{{ $carrier }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Coverage Amount -->
                            <div class="col-md-4">
                                <p class="mb-1"><span class="badge bg-info">Current</span> <span class="text-primary fw-bold" id="displayCoverage">-</span></p>
                                <label class="form-label small">Coverage Amount</label>
                                <input type="number" class="form-control" id="phase2_coverage" step="0.01" placeholder="Amount">
                            </div>
                            
                            <!-- Monthly Premium -->
                            <div class="col-md-4">
                                <p class="mb-1"><span class="badge bg-info">Current</span> <span class="text-primary fw-bold" id="displayPremium">-</span></p>
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
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_name"></span>
                                </div>
                                <input type="text" class="form-control form-control-sm" id="change_name" placeholder="Enter new name if changed">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Phone Number:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_phone"></span>
                                </div>
                                <input type="text" class="form-control form-control-sm" id="change_phone" placeholder="Enter new phone if changed">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Secondary Phone:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_secondary_phone"></span>
                                </div>
                                <input type="text" class="form-control form-control-sm" id="change_secondary_phone" placeholder="Enter secondary phone">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">State:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_state"></span>
                                </div>
                                <select class="form-select form-select-sm" id="change_state">
                                    <option value="">Select State</option>
                                    @foreach($usStates as $state)
                                        <option value="{{ $state }}">{{ $state }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Zip Code:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_zip"></span>
                                </div>
                                <input type="text" class="form-control form-control-sm" id="change_zip" placeholder="Enter zip code">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Date of Birth:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_dob"></span>
                                </div>
                                <input type="date" class="form-control form-control-sm" id="change_dob">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Gender:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_gender"></span>
                                </div>
                                <select class="form-select form-select-sm" id="change_gender">
                                    <option value="">Select</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Birth Place:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_birthplace"></span>
                                </div>
                                <input type="text" class="form-control form-control-sm" id="change_birthplace" placeholder="Enter birth place">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">SSN:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_ssn"></span>
                                </div>
                                <input type="text" class="form-control form-control-sm" id="change_ssn" placeholder="Enter SSN">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Smoker:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_smoker"></span>
                                </div>
                                <select class="form-select form-select-sm" id="change_smoker">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-bold">Height:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_height"></span>
                                </div>
                                <input type="text" class="form-control form-control-sm" id="change_height" placeholder="e.g., 5'10&quot;">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-bold">Weight:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_weight"></span>
                                </div>
                                <input type="text" class="form-control form-control-sm" id="change_weight" placeholder="e.g., 180">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Driving License:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_driving_license"></span>
                                </div>
                                <select class="form-select form-select-sm" id="change_driving_license">
                                    <option value="">Select</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Address:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_address"></span>
                                </div>
                                <input type="text" class="form-control form-control-sm" id="change_address" placeholder="Enter address">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Emergency Contact:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_emergency_contact"></span>
                                </div>
                                <input type="text" class="form-control form-control-sm" id="change_emergency_contact" placeholder="Enter emergency contact">
                            </div>

                            <!-- Medical Information Section -->
                            <div class="col-12 mt-4">
                                <h5 class="border-bottom pb-2 mb-3" style="color: #d4af37;">Medical Information</h5>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Medical Issue:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_medical_issue"></span>
                                </div>
                                <textarea class="form-control form-control-sm" id="change_medical_issue" rows="2" placeholder="Enter medical issues"></textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Medications:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_medications"></span>
                                </div>
                                <textarea class="form-control form-control-sm" id="change_medications" rows="2" placeholder="Enter medications"></textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Doctor Name:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_doctor"></span>
                                </div>
                                <input type="text" class="form-control form-control-sm" id="change_doctor" placeholder="Enter doctor name">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Doctor Phone:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_doctor_phone"></span>
                                </div>
                                <input type="text" class="form-control form-control-sm" id="change_doctor_phone" placeholder="Enter doctor phone">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Doctor Address:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_doctor_address"></span>
                                </div>
                                <input type="text" class="form-control form-control-sm" id="change_doctor_address" placeholder="Enter doctor address">
                            </div>

                            <!-- Policy Information Section -->
                            <div class="col-12 mt-4">
                                <h5 class="border-bottom pb-2 mb-3" style="color: #d4af37;">Policy Information</h5>
                            </div>

                            <!-- Beneficiaries Section -->
                            <div class="col-12">
                                <label class="form-label fw-bold">Beneficiaries:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_beneficiary"></span>
                                </div>
                                <div id="beneficiaries-container-phase3" class="mb-3">
                                    <!-- Beneficiaries will be populated from Phase 2 -->
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="window.addBeneficiaryRowPhase3()">
                                    <i class="bx bx-plus"></i> Add Beneficiary
                                </button>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Policy Type:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_policy_type"></span>
                                </div>
                                <input type="text" class="form-control form-control-sm" id="change_policy_type" placeholder="Enter policy type">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Policy Number:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_policy_number"></span>
                                </div>
                                <input type="text" class="form-control form-control-sm" id="change_policy_number" placeholder="Enter policy number">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Carrier:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_carrier"></span>
                                </div>
                                <select class="form-select form-select-sm" id="change_carrier">
                                    <option value="">Select Carrier</option>
                                    @foreach($insuranceCarriers as $carrier)
                                        <option value="{{ $carrier }}">{{ $carrier }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Coverage Amount:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_coverage"></span>
                                </div>
                                <input type="number" class="form-control form-control-sm" id="change_coverage" step="0.01" placeholder="Enter coverage amount">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Monthly Premium:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_premium"></span>
                                </div>
                                <input type="number" class="form-control form-control-sm" id="change_premium" step="0.01" placeholder="Enter premium amount">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Initial Draft Date:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_draft_date"></span>
                                </div>
                                <input type="date" class="form-control form-control-sm" id="change_draft_date">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Future Draft Date:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_future_draft_date"></span>
                                </div>
                                <input type="date" class="form-control form-control-sm" id="change_future_draft_date">
                            </div>

                            <!-- Banking Information Section -->
                            <div class="col-12 mt-4">
                                <h5 class="border-bottom pb-2 mb-3" style="color: #d4af37;">Banking Information</h5>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Bank Name:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_bank_name"></span>
                                </div>
                                <input type="text" class="form-control form-control-sm" id="change_bank_name" placeholder="Enter bank name">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Account Title:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_account_title"></span>
                                </div>
                                <input type="text" class="form-control form-control-sm" id="change_account_title" placeholder="Enter account title">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Account Type:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_account_type"></span>
                                </div>
                                <select class="form-select form-select-sm" id="change_account_type">
                                    <option value="">Select</option>
                                    <option value="Checking">Checking</option>
                                    <option value="Savings">Savings</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Routing Number:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_routing"></span>
                                </div>
                                <input type="text" class="form-control form-control-sm" id="change_routing" placeholder="Enter routing number">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Account Number:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_account"></span>
                                </div>
                                <input type="text" class="form-control form-control-sm" id="change_account" placeholder="Enter account number">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Verified By:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_verified_by"></span>
                                </div>
                                <input type="text" class="form-control form-control-sm" id="change_verified_by" placeholder="Enter verifier name">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Bank Balance:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_balance"></span>
                                </div>
                                <input type="number" class="form-control form-control-sm" id="change_balance" step="0.01" placeholder="Enter balance">
                            </div>

                            <!-- Card Information Section -->
                            <div class="col-12 mt-4">
                                <h5 class="border-bottom pb-2 mb-3" style="color: #d4af37;"><i class="fas fa-credit-card me-2"></i>Card Information</h5>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Card Number:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_card_number"></span>
                                </div>
                                <input type="text" class="form-control form-control-sm" id="change_card_number" placeholder="Enter card number">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">CVV:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_cvv"></span>
                                </div>
                                <input type="text" class="form-control form-control-sm" id="change_cvv" placeholder="CVV" maxlength="4">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Expiry Date:</label>
                                <div class="mb-2">
                                    <span class="badge bg-info">Current</span>
                                    <span class="text-primary fw-bold" id="orig_expiry_date"></span>
                                </div>
                                <input type="text" class="form-control form-control-sm" id="change_expiry_date" placeholder="MM/YY">
                            </div>

                            <!-- Additional Information -->
                            <div class="col-12 mt-4">
                                <h5 class="border-bottom pb-2 mb-3" style="color: #d4af37;">Additional Information</h5>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Closer Name:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_closer"></div>
                                <input type="text" class="form-control form-control-sm" id="change_closer" placeholder="Changes (if any)">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Source:</label>
                                <div class="p-2 bg-light rounded mb-1" id="orig_source"></div>
                                <input type="text" class="form-control form-control-sm" id="change_source" placeholder="Lead source">
                            </div>

                            <!-- Sale Assignment Section (moved from Phase 2) -->
                            <div class="col-12 mt-4">
                                <h5 class="border-bottom pb-2 mb-3" style="color: #d4af37;"><i class="fas fa-user-tag me-2"></i>Sale Assignment</h5>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Policy Carrier:</label>
                                <select class="form-select" id="phase3_policy_carrier" data-carrier-partner-info='@json($carrierPartnerData)'>
                                    <option value="">Select Carrier</option>
                                    @foreach($carrierPartnerData as $cp)
                                        <option value="{{ $cp['carrier_id'] }}_{{ $cp['partner_id'] }}" 
                                                data-carrier-name="{{ $cp['carrier_name'] }}" 
                                                data-partner-id="{{ $cp['partner_id'] }}"
                                                data-partner-name="{{ $cp['partner_name'] }}"
                                                data-states='@json($cp['states'])'>
                                            {{ $cp['display_name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">State:</label>
                                <select class="form-select" id="phase3_approved_state">
                                    <option value="">Select Carrier First</option>
                                </select>
                            </div>

                            <div class="col-12 mt-4">
                                <h5 class="border-bottom pb-2 mb-3" style="color: #d4af37;"><i class="fas fa-briefcase me-2"></i>Partner Information</h5>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Assigned Partner:</label>
                                <input type="text" class="form-control" id="phase3_assigned_partner" placeholder="Auto-filled from carrier selection" readonly>
                                <input type="hidden" id="phase3_partner_id">
                            </div>

                            <!-- Follow Up Schedule -->
                            <div class="col-12 mt-4">
                                <h5 class="border-bottom pb-2 mb-3" style="color: #d4af37;"><i class="fas fa-calendar-event me-2"></i>Follow Up Schedule</h5>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Follow Up Required:</label>
                                <select class="form-select" id="phase3_followup_required">
                                    <option value="">Select option...</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="col-md-12" id="followup_datetime_field" style="display: none;">
                                <label class="form-label fw-bold">Follow Up Date & Time:</label>
                                <input type="datetime-local" class="form-control" id="phase3_followup_scheduled_at">
                                <small class="text-muted">When should the follow-up call be scheduled?</small>
                            </div>
                        </div>

                        <script>
                        (function() {
                            const followupRequired = document.getElementById('phase3_followup_required');
                            const followupDatetimeField = document.getElementById('followup_datetime_field');
                            const followupScheduledAt = document.getElementById('phase3_followup_scheduled_at');
                            
                            if (followupRequired) {
                                followupRequired.addEventListener('change', function() {
                                    if (this.value === '1') {
                                        followupDatetimeField.style.display = 'block';
                                        followupScheduledAt.setAttribute('required', 'required');
                                    } else {
                                        followupDatetimeField.style.display = 'none';
                                        followupScheduledAt.removeAttribute('required');
                                    }
                                });
                            }

                            // Carrier-Partner State Filtering
                            const carrierSelect = document.getElementById('phase3_policy_carrier');
                            const stateSelect = document.getElementById('phase3_approved_state');
                            const partnerInput = document.getElementById('phase3_assigned_partner');
                            const partnerIdInput = document.getElementById('phase3_partner_id');
                            const allStates = @json($usStates);

                            if (carrierSelect) {
                                carrierSelect.addEventListener('change', function() {
                                    const selectedOption = this.options[this.selectedIndex];
                                    
                                    // Clear state dropdown
                                    stateSelect.innerHTML = '<option value="">Select State</option>';
                                    
                                    if (this.value) {
                                        // Get approved states for this carrier-partner combo
                                        const approvedStates = JSON.parse(selectedOption.dataset.states || '[]');
                                        const partnerName = selectedOption.dataset.partnerName;
                                        const partnerId = selectedOption.dataset.partnerId;
                                        
                                        // Update assigned partner field
                                        if (partnerInput) {
                                            partnerInput.value = partnerName;
                                        }
                                        if (partnerIdInput) {
                                            partnerIdInput.value = partnerId;
                                        }
                                        
                                        // Populate states dropdown with approved states only
                                        approvedStates.forEach(stateCode => {
                                            if (allStates[stateCode]) {
                                                const option = document.createElement('option');
                                                option.value = stateCode;
                                                option.textContent = allStates[stateCode];
                                                stateSelect.appendChild(option);
                                            }
                                        });
                                        
                                        if (approvedStates.length === 0) {
                                            stateSelect.innerHTML = '<option value="">No approved states</option>';
                                        }
                                    } else {
                                        stateSelect.innerHTML = '<option value="">Select Carrier First</option>';
                                        if (partnerInput) partnerInput.value = '';
                                        if (partnerIdInput) partnerIdInput.value = '';
                                    }
                                });
                            }
                        })();
                        </script>

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

@endsection

@section('script')
<script src="{{ URL::asset('build/libs/toastr/build/toastr.min.js') }}"></script>
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.0/dist/echo.iife.js"></script>
<script>
    // Use window scope for global variables to avoid conflicts
    window.autoDialActive = false;
    window.currentLeadIndex = 0;
    window.dialedLeads = new Set(); // Local cache, synced from server
    window.beneficiaryIndexRavens = 0;
    window.isCallActive = false;
    window.autoDialTimeout = null;
    window.currentEventId = null;
    window.pollInterval = null;
    window.currentLeadData = null;
    window.autoSaveInterval = null; // Auto-save form data every 30 seconds
    window.dialStatusData = {}; // Server-synced dial status

    // ===== PERSISTENT DIAL TRACKING =====
    
    /**
     * Load dial status from server and render badges.
     * Called on page load and periodically to show real-time updates from other closers.
     */
    function loadDialStatus() {
        fetch('/ravens/leads/dial-status', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.dialStatusData = data.dials || {};
                renderDialBadges(data.dials, data.current_user_id);
            }
        })
        .catch(error => console.error('Failed to load dial status:', error));
    }

    /**
     * Render dial badges on each lead row showing who dialed it.
     */
    function renderDialBadges(dials, currentUserId) {
        // Clear all existing badges and row highlights
        document.querySelectorAll('.dial-badges').forEach(el => el.innerHTML = '');
        document.querySelectorAll('.lead-row').forEach(row => {
            row.classList.remove('dialed-by-me', 'dialed-by-others', 'dialed');
        });

        // Rebuild local dialedLeads set from server data
        window.dialedLeads = new Set();

        for (const [leadId, dialers] of Object.entries(dials)) {
            const badgeContainer = document.getElementById('dial-badges-' + leadId);
            const row = document.querySelector(`.lead-row[data-lead-id="${leadId}"]`);
            if (!badgeContainer) continue;

            let dialedByMe = false;
            let dialedByOthers = false;

            dialers.forEach(dialer => {
                const badge = document.createElement('span');
                badge.className = 'dial-badge' + (dialer.is_mine ? ' is-mine' : '');
                badge.style.backgroundColor = dialer.color;
                badge.title = dialer.user_name + ' at ' + dialer.dialed_at;
                badge.innerHTML = dialer.initials + '<span class="dial-time">' + dialer.user_name + ' - ' + dialer.dialed_at + '</span>';
                badgeContainer.appendChild(badge);

                if (dialer.is_mine) {
                    dialedByMe = true;
                    window.dialedLeads.add(leadId);
                } else {
                    dialedByOthers = true;
                }
            });

            // Apply row highlights
            if (row) {
                if (dialedByMe) {
                    row.classList.add('dialed-by-me');
                } else if (dialedByOthers) {
                    row.classList.add('dialed-by-others');
                }
            }
        }
    }

    /**
     * Record a dial to the server (persists to DB).
     */
    function recordDial(leadId, outcome = 'dialed') {
        fetch('/ravens/leads/record-dial', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ lead_id: leadId, outcome: outcome })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('✅ Dial recorded for lead', leadId);
                // Refresh dial status to show updated badges
                loadDialStatus();
            }
        })
        .catch(error => console.error('Failed to record dial:', error));
    }

    // Load dial status on page load
    loadDialStatus();
    
    // Refresh dial status every 30 seconds to see other closers' activity
    setInterval(loadDialStatus, 30000);

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
    console.log('  - testRavensFormOpen() - Test full form with first lead');

    // Test function to open Ravens form directly with real lead data
    window.testRavensFormOpen = function() {
        console.log('🧪 TEST: Opening Ravens form for first lead...');
        const firstRow = document.querySelector('.lead-row');
        if (!firstRow) {
            alert('No leads available to test');
            return;
        }
        const leadId = firstRow.getAttribute('data-lead-id');
        const phone = firstRow.getAttribute('data-phone');
        const name = firstRow.querySelector('strong').textContent;
        
        console.log('🧪 TEST: Lead selected:', { leadId, phone, name });
        toastr.info('Opening test form in 2 seconds...', 'Test Mode');
        
        setTimeout(() => {
            console.log('🧪 Calling showRavensFormForCall...');
            showRavensFormForCall(leadId, phone, name, 'connected', 0);
        }, 2000);
    }

    // Save callback note for a lead
    window.saveCallbackNote = function(leadId, note) {
        console.log('💾 Saving callback note for lead', leadId, ':', note);

        fetch('{{ route('ravens.leads.save-callback-note') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                lead_id: leadId,
                note: note
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('✅ Callback note saved:', data.message);
                // Update the timestamp display if note was saved
                if (data.note && data.updated_at) {
                    const input = document.querySelector(`input[data-lead-id="${leadId}"]`);
                    if (input) {
                        const existingTimestamp = input.nextElementSibling;
                        if (existingTimestamp && existingTimestamp.tagName === 'SMALL') {
                            existingTimestamp.innerHTML = `<i class="bx bx-time-five"></i> ${data.updated_at}`;
                        } else {
                            // Create timestamp display
                            const timestamp = document.createElement('small');
                            timestamp.className = 'text-muted d-block mt-1';
                            timestamp.innerHTML = `<i class="bx bx-time-five"></i> ${data.updated_at}`;
                            input.parentNode.insertBefore(timestamp, input.nextSibling);
                        }
                    }
                } else {
                    // Clear timestamp if note was cleared
                    const input = document.querySelector(`input[data-lead-id="${leadId}"]`);
                    if (input) {
                        const existingTimestamp = input.nextElementSibling;
                        if (existingTimestamp && existingTimestamp.tagName === 'SMALL') {
                            existingTimestamp.remove();
                        }
                    }
                }
            } else {
                console.error('❌ Failed to save callback note:', data.message);
            }
        })
        .catch(error => {
            console.error('❌ Error saving callback note:', error);
        });
    }

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
        .then(response => {
            // Check if response is not OK (4xx or 5xx)
            if (!response.ok) {
                if (response.status === 401) {
                    throw new Error('ZOOM_NOT_AUTHORIZED');
                }
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
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
                
                // Record dial persistently to server
                recordDial(leadId, 'dialed');
                
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
            if (error.message === 'ZOOM_NOT_AUTHORIZED') {
                if (confirm('⚠️ Zoom Phone Not Connected\n\nYou need to connect your Zoom Phone account to make calls.\n\nClick OK to connect now.')) {
                    window.location.href = '/zoom/authorize';
                }
            } else {
                alert('❌ Connection failed: ' + error.message + '\n\nPlease try again or contact support.');
            }
        })
        .finally(() => {
            button.disabled = false;
            button.innerHTML = originalText;
        });
    }

    // Get user's zoom number
    window.zoomNumber = '{{ Auth::user()->zoom_number ?? '' }}';
    window.sanitizedZoomNumber = '{{ Auth::user()->sanitized_zoom_number ?? '' }}';

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
        console.log("📞 Starting call detection (show form after 12 seconds)...", { leadId, phoneNumber, leadName });
        
        let isMonitoringActive = true;
        let formShown = false;
        let checkInterval = null;
        
        // Store current call info
        window.currentCallInfo = { leadId, phoneNumber, leadName };
        
        // SHOW LOADING MESSAGE
        toastr.info('Call in progress... Form will open in 12 seconds', 'Please Wait', {
            timeOut: 12000,
            progressBar: true
        });
        
        // Show form after 12 seconds - GUARANTEED
        const formTimer = setTimeout(() => {
            if (isMonitoringActive) {
                console.log('⏰ 12 seconds passed - FORCE showing Ravens form NOW');
                formShown = true;
                
                // Direct form opening - bypass complex checks
                console.log('🔍 Opening form directly for lead:', leadId);
                showRavensFormForCall(leadId, phoneNumber, leadName, 'connected', 0);
            } else {
                console.log('⚠️ Form timer fired but monitoring was stopped');
            }
        }, 12000); // 12 seconds
        
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
                                // Form not shown yet - cancel timer so form will never appear
                                console.log('⛔ Call ended before form shown - canceling 12-second timer');
                                isMonitoringActive = false;
                                clearTimeout(formTimer); // Cancel the 12-second form display timer
                                if (checkInterval) clearInterval(checkInterval);
                                toastr.info(`Call ${status} before form opened`, 'Call Ended');
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
        
        // Stop auto-save and save one final time
        if (window.autoSaveInterval) {
            clearInterval(window.autoSaveInterval);
            window.autoSaveInterval = null;
            console.log('🛑 Auto-save interval cleared');
        }
        autoSaveFormData(true); // Final save before closing
        
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
        console.log(`🎯 FORCING Ravens form to show NOW: ${leadName}`);
        console.log('🔍 Debug: leadId=' + leadId + ', phoneNumber=' + phoneNumber + ', callStatus=' + callStatus);
        
        toastr.success(`Opening form for: ${leadName}`, 'Call Form Ready', { timeOut: 3000 });
        
        // Fetch full lead data from the server to populate the form
        console.log('🌐 Fetching lead data from:', `/ravens/leads/${leadId}/data`);
        fetch(`/ravens/leads/${leadId}/data`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
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
                        height: leadData.height || '',
                        weight: leadData.weight || '',
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
                console.error('ERROR DETAILS:', error.message, error.stack);
                
                // STILL SHOW THE MODAL with whatever data we have
                console.log('⚠️ Showing modal anyway with minimal data due to fetch error...');
                toastr.warning('Could not load all lead details, showing basic form', 'Warning');
                
                // Try to show modal anyway with minimal data
                const callEventData = {
                    event_id: 'call_' + Date.now(),
                    lead_data: {
                        id: leadId,
                        cn_name: leadName,
                        phone_number: phoneNumber,
                        beneficiaries: []
                    },
                    lead_id: leadId,
                    status: callStatus
                };

                console.log('🔍 Checking if showCallModal exists:', typeof showCallModal);
                if (typeof showCallModal === 'function') {
                    console.log('✅ Calling showCallModal with minimal data');
                    showCallModal(callEventData);
                } else {
                    console.error('❌ showCallModal function not available - major JS error');
                    alert('Ravens form function not found. Please refresh the page and try again.');
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
        // Stop auto-save and save one final time before closing
        if (window.autoSaveInterval) {
            clearInterval(window.autoSaveInterval);
            window.autoSaveInterval = null;
            console.log('🛑 Auto-save interval cleared');
        }
        
        // Final save before closing
        autoSaveFormData(true); // true = silent save on close
        
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
        console.log('Navigating to Phase 2...');
        document.getElementById('phase1').style.display = 'none';
        document.getElementById('phase2').style.display = 'block';
        document.getElementById('phase3').style.display = 'none';
    }

    function goToPhase3() {
        console.log('Navigating to Phase 3...');
        
        // Transfer Phase 2 data to Phase 3 displays
        document.getElementById('orig_name').textContent = document.getElementById('phase2_name').value || document.getElementById('displayName').textContent;
        document.getElementById('orig_phone').textContent = document.getElementById('phase2_phone').value || document.getElementById('displayPhone').textContent;
        document.getElementById('orig_secondary_phone').textContent = document.getElementById('phase2_secondary_phone').value || '-';
        document.getElementById('orig_state').textContent = document.getElementById('phase2_state').value || '-';
        document.getElementById('orig_zip').textContent = document.getElementById('phase2_zip').value || '-';
        document.getElementById('orig_dob').textContent = document.getElementById('phase2_dob').value || document.getElementById('displayDOB').textContent;
        document.getElementById('orig_ssn').textContent = document.getElementById('phase2_ssn').value || document.getElementById('displaySSN').textContent;
        document.getElementById('orig_address').textContent = document.getElementById('phase2_address').value || document.getElementById('displayAddress').textContent;
        document.getElementById('orig_emergency_contact').textContent = document.getElementById('phase2_emergency_contact').value || '-';
        document.getElementById('orig_carrier').textContent = document.getElementById('phase2_carrier').value || document.getElementById('displayCarrier').textContent;
        document.getElementById('orig_coverage').textContent = document.getElementById('phase2_coverage').value || document.getElementById('displayCoverage').textContent;
        document.getElementById('orig_premium').textContent = document.getElementById('phase2_premium').value || document.getElementById('displayPremium').textContent;
        
        document.getElementById('phase1').style.display = 'none';
        document.getElementById('phase2').style.display = 'none';
        document.getElementById('phase3').style.display = 'block';
    }

    function goToPhase1() {
        console.log('Returning to Phase 1...');
        document.getElementById('phase1').style.display = 'block';
        document.getElementById('phase2').style.display = 'none';
        document.getElementById('phase3').style.display = 'none';
    }

    // Add beneficiary row dynamically
    window.addBeneficiaryRow = function() {
        const container = document.getElementById('beneficiaries-container-ravens');
        if (!container) {
            console.error('Beneficiary container not found');
            return;
        }
        
        const index = container.querySelectorAll('.beneficiary-ravens-row').length;
        const row = document.createElement('div');
        row.className = 'beneficiary-ravens-row row mb-2 g-2';
        row.innerHTML = `
            <div class="col-md-4">
                <input type="text" class="form-control beneficiary-name-ravens" 
                       placeholder="Beneficiary Name" required>
            </div>
            <div class="col-md-3">
                <input type="date" class="form-control beneficiary-dob-ravens" required>
            </div>
            <div class="col-md-3">
                <select class="form-select beneficiary-relation-ravens">
                    <option value="">Relation</option>
                    <option value="Spouse">Spouse</option>
                    <option value="Child">Child</option>
                    <option value="Parent">Parent</option>
                    <option value="Sibling">Sibling</option>
                    <option value="Grandchild">Grandchild</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm w-100" onclick="this.closest('.beneficiary-ravens-row').remove()">
                    <i class="bx bx-trash"></i>
                </button>
            </div>
        `;
        
        container.appendChild(row);
        console.log('✅ Beneficiary row added');
    }

    // Add beneficiary row for Phase 3
    window.addBeneficiaryRowPhase3 = function() {
        const container = document.getElementById('beneficiaries-container-phase3');
        if (!container) {
            console.error('Phase 3 Beneficiary container not found');
            return;
        }
        
        const row = document.createElement('div');
        row.className = 'beneficiary-phase3-row row mb-2 g-2';
        row.innerHTML = `
            <div class="col-md-4">
                <input type="text" class="form-control form-control-sm beneficiary-name-phase3" 
                       placeholder="Beneficiary Name">
            </div>
            <div class="col-md-3">
                <input type="date" class="form-control form-control-sm beneficiary-dob-phase3">
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-sm beneficiary-relation-phase3">
                    <option value="">Relation</option>
                    <option value="Spouse">Spouse</option>
                    <option value="Child">Child</option>
                    <option value="Parent">Parent</option>
                    <option value="Sibling">Sibling</option>
                    <option value="Grandchild">Grandchild</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm w-100" onclick="this.closest('.beneficiary-phase3-row').remove()">
                    <i class="bx bx-trash"></i>
                </button>
            </div>
        `;
        
        container.appendChild(row);
        console.log('✅ Phase 3 Beneficiary row added');
    }

    // REMOVED DUPLICATE BROKEN showCallModal FUNCTION
    
    function goToPhase3() {
        populatePhase3WithData();
        copyBeneficiariesFromPhase2ToPhase3();
        document.getElementById('phase1').style.display = 'none';
        document.getElementById('phase2').style.display = 'none';
        document.getElementById('phase3').style.display = 'block';
    }

    // Copy beneficiaries from Phase 2 to Phase 3
    function copyBeneficiariesFromPhase2ToPhase3() {
        const phase2Container = document.getElementById('beneficiaries-container-ravens');
        const phase3Container = document.getElementById('beneficiaries-container-phase3');
        
        if (!phase2Container || !phase3Container) {
            console.error('Beneficiary containers not found');
            return;
        }

        // Clear Phase 3 container
        phase3Container.innerHTML = '';

        // Get all beneficiaries from Phase 2
        const phase2Rows = phase2Container.querySelectorAll('.beneficiary-ravens-row');
        
        if (phase2Rows.length === 0) {
            // Add one empty row if no beneficiaries
            window.addBeneficiaryRowPhase3();
            return;
        }

        // Copy each beneficiary to Phase 3
        phase2Rows.forEach((phase2Row) => {
            const name = phase2Row.querySelector('.beneficiary-name-ravens')?.value || '';
            const dob = phase2Row.querySelector('.beneficiary-dob-ravens')?.value || '';
            const relation = phase2Row.querySelector('.beneficiary-relation-ravens')?.value || '';

            const row = document.createElement('div');
            row.className = 'beneficiary-phase3-row row mb-2 g-2';
            row.innerHTML = `
                <div class="col-md-4">
                    <input type="text" class="form-control form-control-sm beneficiary-name-phase3" 
                           placeholder="Beneficiary Name" value="${name}">
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control form-control-sm beneficiary-dob-phase3" value="${dob}">
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-sm beneficiary-relation-phase3">
                        <option value="">Relation</option>
                        <option value="Spouse" ${relation === 'Spouse' ? 'selected' : ''}>Spouse</option>
                        <option value="Child" ${relation === 'Child' ? 'selected' : ''}>Child</option>
                        <option value="Parent" ${relation === 'Parent' ? 'selected' : ''}>Parent</option>
                        <option value="Sibling" ${relation === 'Sibling' ? 'selected' : ''}>Sibling</option>
                        <option value="Grandchild" ${relation === 'Grandchild' ? 'selected' : ''}>Grandchild</option>
                        <option value="Other" ${relation === 'Other' ? 'selected' : ''}>Other</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm w-100" onclick="this.closest('.beneficiary-phase3-row').remove()">
                        <i class="bx bx-trash"></i>
                    </button>
                </div>
            `;
            phase3Container.appendChild(row);
        });

        console.log(`✅ Copied ${phase2Rows.length} beneficiaries to Phase 3`);
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
        document.getElementById('orig_secondary_phone').textContent = ld.secondary_phone_number || 'N/A';
        document.getElementById('orig_state').textContent = ld.state || 'N/A';
        document.getElementById('orig_zip').textContent = ld.zip_code || 'N/A';
        document.getElementById('orig_dob').textContent = formatDate(ld.date_of_birth);
        document.getElementById('orig_gender').textContent = ld.gender || 'N/A';
        document.getElementById('orig_birthplace').textContent = ld.birth_place || 'N/A';
        document.getElementById('orig_ssn').textContent = document.getElementById('phase2_ssn').value || ld.ssn || 'N/A';
        document.getElementById('orig_smoker').textContent = ld.smoker == 1 ? 'Yes' : 'No';
        document.getElementById('orig_height').textContent = ld.height || 'N/A';
        document.getElementById('orig_weight').textContent = ld.weight ? ld.weight + ' lbs' : 'N/A';
        document.getElementById('orig_driving_license').textContent = ld.driving_license || 'N/A';
        // Address fallback: use address, else state, else birth place
        let addressDisplay3 = ld.address;
        if (!addressDisplay3 || addressDisplay3.trim() === '') {
            addressDisplay3 = ld.state || ld.birth_place || 'N/A';
        }
        document.getElementById('orig_address').textContent = addressDisplay3;
        document.getElementById('orig_emergency_contact').textContent = ld.emergency_contact || 'N/A';

        // Medical Information
        document.getElementById('orig_medical_issue').textContent = ld.medical_issue || 'N/A';
        document.getElementById('orig_medications').textContent = ld.medications || 'N/A';
        document.getElementById('orig_doctor').textContent = ld.doctor_name || 'N/A';
        document.getElementById('orig_doctor_phone').textContent = ld.doctor_number || 'N/A';
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
        // Note: orig_beneficiary_dob display has been removed in favor of showing all beneficiaries with DOBs
        document.getElementById('orig_policy_type').textContent = ld.policy_type || 'N/A';
        document.getElementById('orig_policy_number').textContent = ld.policy_number || 'N/A';
        document.getElementById('orig_carrier').textContent = document.getElementById('phase2_carrier').value || 'N/A';
        document.getElementById('orig_coverage').textContent = document.getElementById('phase2_coverage').value ? '$' + parseFloat(document.getElementById('phase2_coverage').value).toLocaleString() : 'N/A';
        document.getElementById('orig_premium').textContent = document.getElementById('phase2_premium').value ? '$' + parseFloat(document.getElementById('phase2_premium').value).toFixed(2) : 'N/A';
        document.getElementById('orig_draft_date').textContent = formatDate(ld.initial_draft_date);
        document.getElementById('orig_future_draft_date').textContent = formatDate(ld.future_draft_date);

        // Banking Information
        document.getElementById('orig_bank_name').textContent = ld.bank_name || 'N/A';
        document.getElementById('orig_account_title').textContent = ld.account_title || 'N/A';
        document.getElementById('orig_account_type').textContent = ld.account_type || 'N/A';
        document.getElementById('orig_routing').textContent = ld.routing_number || 'N/A';
        document.getElementById('orig_account').textContent = ld.account_number || 'N/A';
        document.getElementById('orig_verified_by').textContent = ld.verified_by || 'N/A';
        document.getElementById('orig_balance').textContent = ld.bank_balance ? '$' + parseFloat(ld.bank_balance).toFixed(2) : 'N/A';

        // Card Information
        document.getElementById('orig_card_number').textContent = ld.card_number || 'N/A';
        document.getElementById('orig_cvv').textContent = ld.cvv || 'N/A';
        document.getElementById('orig_expiry_date').textContent = ld.expiry_date || 'N/A';

        // Additional Information
        document.getElementById('orig_closer').textContent = ld.closer_name || 'N/A';
        document.getElementById('orig_source').textContent = ld.source || 'N/A';

        // Pre-fill change inputs with Phase 2 data
        document.getElementById('change_name').value = ld.cn_name || '';
        document.getElementById('change_phone').value = ld.phone_number || '';
        document.getElementById('change_secondary_phone').value = ld.secondary_phone_number || '';
        document.getElementById('change_state').value = ld.state || '';
        document.getElementById('change_zip').value = ld.zip_code || '';
        document.getElementById('change_dob').value = formatDateInput(document.getElementById('phase2_dob').value);
        document.getElementById('change_ssn').value = document.getElementById('phase2_ssn').value || '';
        document.getElementById('change_emergency_contact').value = ld.emergency_contact || '';
        // Beneficiary is now handled separately in beneficiaries array
        document.getElementById('change_carrier').value = document.getElementById('phase2_carrier').value || '';
        document.getElementById('change_coverage').value = document.getElementById('phase2_coverage').value || '';
        document.getElementById('change_premium').value = document.getElementById('phase2_premium').value || '';
        document.getElementById('change_future_draft_date').value = formatDateInput(ld.future_draft_date);
        document.getElementById('change_doctor_phone').value = ld.doctor_number || '';
        document.getElementById('change_driving_license').value = ld.driving_license || '';
        document.getElementById('change_height').value = ld.height || '';
        document.getElementById('change_weight').value = ld.weight || '';
        document.getElementById('change_card_number').value = ld.card_number || '';
        document.getElementById('change_cvv').value = ld.cvv || '';
        document.getElementById('change_expiry_date').value = ld.expiry_date || '';
        document.getElementById('change_policy_number').value = ld.policy_number || '';
        document.getElementById('change_account_title').value = ld.account_title || '';
        document.getElementById('change_source').value = ld.source || '';
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
        safeSetText('displaySecondaryPhone', leadData.secondary_phone_number || 'N/A');
        safeSetText('displayState', leadData.state || 'N/A');
        safeSetText('displayZipCode', leadData.zip_code || 'N/A');
        safeSetText('displayDOB', formatDateDisplay(leadData.date_of_birth));
        safeSetText('displaySSN', leadData.ssn || 'Not available');
        // Address fallback: use address, else state, else birth place
        let addressDisplay = leadData.address;
        if (!addressDisplay || addressDisplay.trim() === '') {
            addressDisplay = leadData.state || leadData.birth_place || 'Not available';
        }
        safeSetText('displayAddress', addressDisplay);
        safeSetText('displayEmergencyContact', leadData.emergency_contact || 'N/A');
        
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
        safeSetValue('phase2_secondary_phone', leadData.secondary_phone_number || '');
        safeSetValue('phase2_state', leadData.state || '');
        safeSetValue('phase2_zip', leadData.zip_code || '');
        safeSetValue('phase2_dob', formatDateInput(leadData.date_of_birth));
        safeSetValue('phase2_ssn', leadData.ssn || '');
        // Pre-fill address field with fallback
        let addressValue = leadData.address;
        if (!addressValue || addressValue.trim() === '') {
            addressValue = leadData.state || leadData.birth_place || '';
        }
        safeSetValue('phase2_address', addressValue);
        safeSetValue('phase2_emergency_contact', leadData.emergency_contact || '');
        
        // Clear existing beneficiary rows
        const beneficiaryContainer = document.getElementById('beneficiaries-container-ravens');
        if (beneficiaryContainer) {
            beneficiaryContainer.innerHTML = '';
            window.beneficiaryIndexRavens = 0;
            
            // Populate beneficiaries from lead data
            if (leadData.beneficiaries && leadData.beneficiaries.length > 0) {
                leadData.beneficiaries.forEach((beneficiary, index) => {
                const row = document.createElement('div');
                row.className = 'beneficiary-ravens-row row mb-2 g-2';
                row.innerHTML = `
                    <div class="col-md-4">
                        <input type="text" class="form-control beneficiary-name-ravens" 
                               placeholder="Beneficiary Name" value="${beneficiary.name || ''}" required>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control beneficiary-dob-ravens" 
                               value="${formatDateInput(beneficiary.dob || '')}" required>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select beneficiary-relation-ravens">
                            <option value="">Relation</option>
                            <option value="Spouse" ${(beneficiary.relation || '') === 'Spouse' ? 'selected' : ''}>Spouse</option>
                            <option value="Child" ${(beneficiary.relation || '') === 'Child' ? 'selected' : ''}>Child</option>
                            <option value="Parent" ${(beneficiary.relation || '') === 'Parent' ? 'selected' : ''}>Parent</option>
                            <option value="Sibling" ${(beneficiary.relation || '') === 'Sibling' ? 'selected' : ''}>Sibling</option>
                            <option value="Grandchild" ${(beneficiary.relation || '') === 'Grandchild' ? 'selected' : ''}>Grandchild</option>
                            <option value="Other" ${(beneficiary.relation || '') === 'Other' ? 'selected' : ''}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm w-100 remove-beneficiary-ravens">
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
                row.className = 'beneficiary-ravens-row row mb-2 g-2';
                row.innerHTML = `
                    <div class="col-md-4">
                        <input type="text" class="form-control beneficiary-name-ravens" 
                               placeholder="Beneficiary Name" required>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control beneficiary-dob-ravens" required>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select beneficiary-relation-ravens">
                            <option value="">Relation</option>
                            <option value="Spouse">Spouse</option>
                            <option value="Child">Child</option>
                            <option value="Parent">Parent</option>
                            <option value="Sibling">Sibling</option>
                            <option value="Grandchild">Grandchild</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm w-100 remove-beneficiary-ravens">
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
            
            // Start auto-save interval (save every 30 seconds)
            if (window.autoSaveInterval) {
                clearInterval(window.autoSaveInterval);
            }
            window.autoSaveInterval = setInterval(() => {
                autoSaveFormData(false); // Show auto-save notification
            }, 30000); // Every 30 seconds
            console.log('💾 Auto-save started (every 30 seconds)');
            
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
                
                // Start auto-save interval for fallback case too
                if (window.autoSaveInterval) {
                    clearInterval(window.autoSaveInterval);
                }
                window.autoSaveInterval = setInterval(() => {
                    autoSaveFormData(false); // Show auto-save notification
                }, 30000); // Every 30 seconds
                console.log('💾 Auto-save started (fallback, every 30 seconds)');
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
                height: '5ft 10in',
                weight: '180',
                address: '123 Test Street, Test City, TX 12345',
                beneficiary: 'Jane Test Beneficiary',
                carrier_name: 'Test Insurance Co',
                coverage_amount: '100000',
                monthly_premium: '75.50',
                closer_name: @json(Auth::user()->name ?? 'Test Closer'),
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
     * Auto-save form data silently (called every 30 seconds and on form close)
     */
    function autoSaveFormData(isSilent = false) {
        const leadId = window.currentLeadData?.id;
        
        if (!leadId) {
            console.log('⚠️ Auto-save skipped: No lead ID');
            return;
        }

        // Collect beneficiary data
        const beneficiaries = [];
        document.querySelectorAll('.beneficiary-phase3-row').forEach((row) => {
            const name = row.querySelector('.beneficiary-name-phase3')?.value;
            const dob = row.querySelector('.beneficiary-dob-phase3')?.value;
            const relation = row.querySelector('.beneficiary-relation-phase3')?.value;
            if (name) {
                beneficiaries.push({ name: name, dob: dob || null, relation: relation || null });
            }
        });
        
        const formData = {
            lead_id: leadId,
            cn_name: document.getElementById('change_name')?.value || null,
            phone_number: document.getElementById('change_phone')?.value || null,
            secondary_phone_number: document.getElementById('change_secondary_phone')?.value || null,
            state: document.getElementById('change_state')?.value || null,
            zip_code: document.getElementById('change_zip')?.value || null,
            date_of_birth: document.getElementById('change_dob')?.value || null,
            ssn: document.getElementById('change_ssn')?.value || null,
            gender: document.getElementById('change_gender')?.value || null,
            address: document.getElementById('change_address')?.value || null,
            emergency_contact: document.getElementById('change_emergency_contact')?.value || null,
            driving_license: document.getElementById('change_driving_license')?.value || null,
            birth_place: document.getElementById('change_birthplace')?.value || null,
            height: document.getElementById('change_height')?.value || null,
            weight: document.getElementById('change_weight')?.value || null,
            smoker: document.getElementById('change_smoker')?.value || null,
            medical_issue: document.getElementById('change_medical_issue')?.value || null,
            medications: document.getElementById('change_medications')?.value || null,
            doctor_name: document.getElementById('change_doctor')?.value || null,
            doctor_number: document.getElementById('change_doctor_phone')?.value || null,
            doctor_address: document.getElementById('change_doctor_address')?.value || null,
            beneficiaries: beneficiaries,
            policy_type: document.getElementById('change_policy_type')?.value || null,
            carrier_name: document.getElementById('change_carrier')?.value || null,
            coverage_amount: document.getElementById('change_coverage')?.value || null,
            monthly_premium: document.getElementById('change_premium')?.value || null,
            initial_draft_date: document.getElementById('change_draft_date')?.value || null,
            future_draft_date: document.getElementById('change_future_draft_date')?.value || null,
            bank_name: document.getElementById('change_bank_name')?.value || null,
            account_type: document.getElementById('change_account_type')?.value || null,
            routing_number: document.getElementById('change_routing')?.value || null,
            account_number: document.getElementById('change_account')?.value || null,
            account_verified_by: document.getElementById('change_verified_by')?.value || null,
            bank_balance: document.getElementById('change_balance')?.value || null,
            card_number: document.getElementById('change_card_number')?.value || null,
            cvv: document.getElementById('change_cvv')?.value || null,
            expiry_date: document.getElementById('change_expiry_date')?.value || null,
            closer_name: document.getElementById('change_closer')?.value || null,
            policy_number: document.getElementById('change_policy_number')?.value || null,
            account_title: document.getElementById('change_account_title')?.value || null,
            source: document.getElementById('change_source')?.value || null,
        };
        
        // Check if any data has actually been entered (besides default values)
        const hasData = Object.values(formData).some(val => {
            if (val === null || val === '' || val === leadId) return false;
            if (Array.isArray(val) && val.length === 0) return false;
            return true;
        });
        
        if (!hasData) {
            if (!isSilent) {
                console.log('⚠️ Auto-save skipped: No data entered yet');
            }
            return;
        }
        
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
                if (!isSilent) {
                    console.log('💾 Auto-save successful');
                    toastr.success('Form data saved', 'Auto-saved', { timeOut: 2000 });
                }
            } else {
                console.error('⚠️ Auto-save failed:', data.message);
            }
        })
        .catch(error => {
            console.error('❌ Auto-save error:', error);
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

        // Collect beneficiary data from Phase 3
        const beneficiaries = [];
        document.querySelectorAll('.beneficiary-phase3-row').forEach((row) => {
            const name = row.querySelector('.beneficiary-name-phase3')?.value;
            const dob = row.querySelector('.beneficiary-dob-phase3')?.value;
            const relation = row.querySelector('.beneficiary-relation-phase3')?.value;
            if (name) {
                beneficiaries.push({ name: name, dob: dob || null, relation: relation || null });
            }
        });
        
        const formData = {
            lead_id: leadId,
            cn_name: document.getElementById('change_name')?.value || null,
            phone_number: document.getElementById('change_phone')?.value || null,
            secondary_phone_number: document.getElementById('change_secondary_phone')?.value || null,
            state: document.getElementById('change_state')?.value || null,
            zip_code: document.getElementById('change_zip')?.value || null,
            date_of_birth: document.getElementById('change_dob')?.value || null,
            ssn: document.getElementById('change_ssn')?.value || null,
            gender: document.getElementById('change_gender')?.value || null,
            address: document.getElementById('change_address')?.value || null,
            emergency_contact: document.getElementById('change_emergency_contact')?.value || null,
            driving_license: document.getElementById('change_driving_license')?.value || null,
            birth_place: document.getElementById('change_birthplace')?.value || null,
            height: document.getElementById('change_height')?.value || null,
            weight: document.getElementById('change_weight')?.value || null,
            smoker: document.getElementById('change_smoker')?.value || null,
            medical_issue: document.getElementById('change_medical_issue')?.value || null,
            medications: document.getElementById('change_medications')?.value || null,
            doctor_name: document.getElementById('change_doctor')?.value || null,
            doctor_number: document.getElementById('change_doctor_phone')?.value || null,
            doctor_address: document.getElementById('change_doctor_address')?.value || null,
            beneficiaries: beneficiaries,
            policy_type: document.getElementById('change_policy_type')?.value || null,
            carrier_name: document.getElementById('change_carrier')?.value || null,
            coverage_amount: document.getElementById('change_coverage')?.value || null,
            monthly_premium: document.getElementById('change_premium')?.value || null,
            initial_draft_date: document.getElementById('change_draft_date')?.value || null,
            future_draft_date: document.getElementById('change_future_draft_date')?.value || null,
            bank_name: document.getElementById('change_bank_name')?.value || null,
            account_type: document.getElementById('change_account_type')?.value || null,
            routing_number: document.getElementById('change_routing')?.value || null,
            account_number: document.getElementById('change_account')?.value || null,
            account_verified_by: document.getElementById('change_verified_by')?.value || null,
            bank_balance: document.getElementById('change_balance')?.value || null,
            card_number: document.getElementById('change_card_number')?.value || null,
            cvv: document.getElementById('change_cvv')?.value || null,
            expiry_date: document.getElementById('change_expiry_date')?.value || null,
            closer_name: document.getElementById('change_closer')?.value || null,
            policy_number: document.getElementById('change_policy_number')?.value || null,
            account_title: document.getElementById('change_account_title')?.value || null,
            source: document.getElementById('change_source')?.value || null,
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
        
        // Collect beneficiary data from Phase 3
        const beneficiaries = [];
        document.querySelectorAll('.beneficiary-phase3-row').forEach((row) => {
            const name = row.querySelector('.beneficiary-name-phase3')?.value;
            const dob = row.querySelector('.beneficiary-dob-phase3')?.value;
            const relation = row.querySelector('.beneficiary-relation-phase3')?.value;
            if (name) {
                beneficiaries.push({ name: name, dob: dob || null, relation: relation || null });
            }
        });
        
        // Collect all form data
        const formData = {
            lead_id: leadId,
            cn_name: document.getElementById('phase2_name')?.value || document.getElementById('change_name')?.value || null,
            phone_number: document.getElementById('phase2_phone')?.value || document.getElementById('change_phone')?.value || null,
            secondary_phone_number: document.getElementById('phase2_secondary_phone')?.value || document.getElementById('change_secondary_phone')?.value || null,
            state: document.getElementById('phase2_state')?.value || document.getElementById('change_state')?.value || null,
            zip_code: document.getElementById('phase2_zip')?.value || document.getElementById('change_zip')?.value || null,
            date_of_birth: document.getElementById('phase2_dob')?.value || document.getElementById('change_dob')?.value || null,
            ssn: document.getElementById('phase2_ssn')?.value || document.getElementById('change_ssn')?.value || null,
            gender: document.getElementById('phase2_gender')?.value || document.getElementById('change_gender')?.value || null,
            address: document.getElementById('phase2_address')?.value || document.getElementById('change_address')?.value || null,
            emergency_contact: document.getElementById('phase2_emergency_contact')?.value || document.getElementById('change_emergency_contact')?.value || null,
            driving_license: document.getElementById('change_driving_license')?.value || null,
            birth_place: document.getElementById('change_birthplace')?.value || null,
            height: document.getElementById('change_height')?.value || null,
            weight: document.getElementById('change_weight')?.value || null,
            smoker: document.getElementById('change_smoker')?.value || null,
            medical_issue: document.getElementById('change_medical_issue')?.value || null,
            medications: document.getElementById('change_medications')?.value || null,
            doctor_name: document.getElementById('change_doctor')?.value || null,
            doctor_number: document.getElementById('change_doctor_phone')?.value || null,
            doctor_address: document.getElementById('change_doctor_address')?.value || null,
            beneficiaries: beneficiaries,
            policy_type: document.getElementById('change_policy_type')?.value || null,
            carrier_name: document.getElementById('phase2_carrier')?.value || document.getElementById('change_carrier')?.value || null,
            coverage_amount: document.getElementById('phase2_coverage')?.value || document.getElementById('change_coverage')?.value || null,
            monthly_premium: document.getElementById('phase2_premium')?.value || document.getElementById('change_premium')?.value || null,
            initial_draft_date: document.getElementById('change_draft_date')?.value || null,
            future_draft_date: document.getElementById('change_future_draft_date')?.value || null,
            bank_name: document.getElementById('change_bank_name')?.value || null,
            account_type: document.getElementById('change_account_type')?.value || null,
            routing_number: document.getElementById('change_routing')?.value || null,
            account_number: document.getElementById('phase2_account_number')?.value || document.getElementById('change_account')?.value || null,
            account_verified_by: document.getElementById('change_verified_by')?.value || null,
            bank_balance: document.getElementById('change_balance')?.value || null,
            card_number: document.getElementById('change_card_number')?.value || null,
            cvv: document.getElementById('change_cvv')?.value || null,
            expiry_date: document.getElementById('change_expiry_date')?.value || null,
            closer_name: @json(Auth::user()->name ?? 'Unknown'),
            policy_number: document.getElementById('change_policy_number')?.value || null,
            account_title: document.getElementById('change_account_title')?.value || null,
            source: document.getElementById('change_source')?.value || null,
            
            // Extract carrier and partner info from the combined value
            insurance_carrier_id: (() => {
                const carrierSelect = document.getElementById('phase3_policy_carrier');
                const selectedOption = carrierSelect?.options[carrierSelect.selectedIndex];
                return selectedOption?.dataset?.carrierName || null;
            })(),
            partner_id: document.getElementById('phase3_partner_id')?.value || null,
            assigned_partner: document.getElementById('phase3_assigned_partner')?.value || null,
            state: document.getElementById('phase3_approved_state')?.value || null,
            
            followup_required: document.getElementById('phase3_followup_required')?.value || null,
            followup_scheduled_at: document.getElementById('phase3_followup_scheduled_at')?.value || null,
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
        fetch('{{ route('ravens.leads.dispose') }}', {
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
@endsection
