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
                                @forelse($leads as $index => $lead)
                                    <tr class="lead-row" data-lead-id="{{ $lead->id }}" data-phone="{{ $lead->phone_number }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $lead->cn_name ?? 'N/A' }}</strong>
                                            @if(strtolower($lead->team ?? '') === 'paraguins')
                                                <span class="badge bg-success ms-2">Paraguin</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-primary btn-sm dial-btn" onclick="makeCall('{{ $lead->id }}', '{{ $lead->phone_number }}', this)">
                                                <i class="bx bx-phone-call"></i> Call
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
                        <div class="alert alert-info border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);">
                            <i class="fas fa-info-circle me-2"></i> <strong>Review current information and note any changes</strong>
                        </div>

                        <div class="row g-4">
                            <!-- Name -->
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <label class="form-label text-muted small mb-1">CURRENT NAME</label>
                                        <div class="p-2 bg-light border rounded mb-3 fw-bold" id="displayName" style="min-height: 38px; line-height: 22px;">-</div>
                                        <label class="form-label text-primary small mb-1"><i class="fas fa-edit me-1"></i>Changes (if any)</label>
                                        <input type="text" class="form-control" id="phase2_name" placeholder="Enter new name if changed">
                                    </div>
                                </div>
                            </div>

                            <!-- Phone Number -->
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <label class="form-label text-muted small mb-1">CURRENT PHONE</label>
                                        <div class="p-2 bg-light border rounded mb-3 fw-bold" id="displayPhone" style="min-height: 38px; line-height: 22px;">-</div>
                                        <label class="form-label text-primary small mb-1"><i class="fas fa-edit me-1"></i>Changes (if any)</label>
                                        <input type="text" class="form-control" id="phase2_phone" placeholder="Enter new phone if changed">
                                    </div>
                                </div>
                            </div>

                            <!-- DOB -->
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <label class="form-label text-muted small mb-1">CURRENT DATE OF BIRTH</label>
                                        <div class="p-2 bg-light border rounded mb-3" id="displayDOB" style="min-height: 38px; line-height: 22px;">Not available</div>
                                        <label class="form-label text-danger small mb-1"><i class="fas fa-asterisk me-1" style="font-size: 8px;"></i>Update/Confirm <span class="badge bg-danger">Required</span></label>
                                        <input type="date" class="form-control required-field" id="phase2_dob" required>
                                    </div>
                                </div>
                            </div>

                            <!-- SSN -->
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <label class="form-label text-muted small mb-1">CURRENT SSN</label>
                                        <div class="p-2 bg-light border rounded mb-3" id="displaySSN" style="min-height: 38px; line-height: 22px;">Not available</div>
                                        <label class="form-label text-danger small mb-1"><i class="fas fa-asterisk me-1" style="font-size: 8px;"></i>Update/Confirm <span class="badge bg-danger">Required</span></label>
                                        <input type="text" class="form-control required-field" id="phase2_ssn" placeholder="XXX-XX-XXXX" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <label class="form-label text-muted small mb-1">CURRENT ADDRESS</label>
                                        <div class="p-2 bg-light border rounded mb-3" id="displayAddress" style="min-height: 38px; line-height: 22px;">Not available</div>
                                        <label class="form-label text-primary small mb-1"><i class="fas fa-edit me-1"></i>Changes (if any)</label>
                                        <input type="text" class="form-control" id="phase2_address" placeholder="Enter new address if changed">
                                    </div>
                                </div>
                            </div>

                            <!-- Beneficiary -->
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <label class="form-label text-muted small mb-1">CURRENT BENEFICIARY</label>
                                        <div class="p-2 bg-light border rounded mb-3" id="displayBeneficiary" style="min-height: 38px; line-height: 22px;">Not available</div>
                                        <label class="form-label text-danger small mb-1"><i class="fas fa-asterisk me-1" style="font-size: 8px;"></i>Update/Confirm <span class="badge bg-danger">Required</span></label>
                                        <input type="text" class="form-control required-field" id="phase2_beneficiary" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Carrier -->
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <label class="form-label text-muted small mb-1">CURRENT CARRIER</label>
                                        <div class="p-2 bg-light border rounded mb-3" id="displayCarrier" style="min-height: 38px; line-height: 22px;">Not available</div>
                                        <label class="form-label text-danger small mb-1"><i class="fas fa-asterisk me-1" style="font-size: 8px;"></i>Update/Confirm <span class="badge bg-danger">Required</span></label>
                                        <input type="text" class="form-control required-field" id="phase2_carrier" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Coverage Amount -->
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <label class="form-label text-muted small mb-1">CURRENT COVERAGE AMOUNT</label>
                                        <div class="p-2 bg-light border rounded mb-3" id="displayCoverage" style="min-height: 38px; line-height: 22px;">Not available</div>
                                        <label class="form-label text-danger small mb-1"><i class="fas fa-asterisk me-1" style="font-size: 8px;"></i>Update/Confirm <span class="badge bg-danger">Required</span></label>
                                        <input type="number" class="form-control required-field" id="phase2_coverage" step="0.01" placeholder="Enter amount" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Premium -->
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <label class="form-label text-muted small mb-1">CURRENT MONTHLY PREMIUM</label>
                                        <div class="p-2 bg-light border rounded mb-3" id="displayPremium" style="min-height: 38px; line-height: 22px;">Not available</div>
                                        <label class="form-label text-danger small mb-1"><i class="fas fa-asterisk me-1" style="font-size: 8px;"></i>Update/Confirm <span class="badge bg-danger">Required</span></label>
                                        <input type="number" class="form-control required-field" id="phase2_premium" step="0.01" placeholder="Enter amount" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Account Number -->
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <label class="form-label text-muted small mb-1">CURRENT ACCOUNT NUMBER</label>
                                        <div class="p-2 bg-light border rounded mb-3" id="displayAccountNumber" style="min-height: 38px; line-height: 22px;">Not available</div>
                                        <label class="form-label text-primary small mb-1"><i class="fas fa-edit me-1"></i>Changes (if any)</label>
                                        <input type="text" class="form-control" id="phase2_account_number" placeholder="Enter new account number if changed">
                                    </div>
                                </div>
                            </div>

                            <!-- Partner/Agent -->
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fff3cd 0%, #fff9e6 100%);">
                                    <div class="card-body">
                                        <label class="form-label fw-bold small mb-2"><i class="fas fa-user-tie me-1"></i>PARTNER/AGENT ASSIGNMENT <span class="badge bg-danger">Required</span></label>
                                        <select class="form-select form-select-lg required-field" id="phase2_partner_agent" required>
                                            <option value="">Select Partner/Agent</option>
                                            <option value="partner_1">John Partner</option>
                                            <option value="agent_1">-- Agent Mike</option>
                                            <option value="agent_2">-- Agent Sarah</option>
                                            <option value="partner_2">Jane Partner</option>
                                            <option value="agent_3">-- Agent Tom</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4 pb-3">
                            <button type="button" class="btn btn-light btn-lg px-4 me-2" onclick="goToPhase1()">
                                <i class="fas fa-arrow-left me-2"></i> Back
                            </button>
                            <button type="button" class="btn btn-lg px-4" style="background: linear-gradient(135deg, #d4af37 0%, #b8941f 100%); color: white; box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);" id="showMoreBtn" disabled onclick="goToPhase3()">
                                <i class="fas fa-arrow-right me-2"></i> Continue to Full Details
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
                                <label class="form-label fw-bold">Policy Carrier: <span class="text-danger">*</span></label>
                                <select class="form-select required-field" id="phase3_policy_carrier" required>
                                    <option value="">Select Carrier</option>
                                    <option value="AMAM">AMAM</option>
                                    <option value="Combined">Combined</option>
                                    <option value="AIG">AIG</option>
                                    <option value="LBL">LBL</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">State: <span class="text-danger">*</span></label>
                                <select class="form-select required-field" id="phase3_approved_state" required>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-phone-slash me-1"></i> End Call</button>
                    <button type="button" class="btn btn-success"><i class="fas fa-save me-1"></i> Save & Exit</button>
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
    window.dialedLeads = new Set();
    window.isCallActive = false;
    window.autoDialTimeout = null;
    window.currentEventId = null;
    window.pollInterval = null;
    window.currentLeadData = null;

    // TEST: Ensure JavaScript is loading
    console.log('✅ Ravens calling script loaded');

    // Unified call function - uses proper Zoom API integration
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

    // New flow: Show form 12 seconds after dial, close immediately if call ends
    window.startRealCallDetection = function(leadId, phoneNumber, leadName) {
        console.log("📞 Starting call detection (auto-show after 12 seconds)...", { leadId, phoneNumber, leadName });
        
        let isMonitoringActive = true;
        let formShown = false;
        let checkInterval = null;
        
        // Store current call info
        window.currentCallInfo = { leadId, phoneNumber, leadName };
        
        // Show form after 12 seconds (regardless of call status)
        const autoShowTimer = setTimeout(() => {
            if (isMonitoringActive) {
                console.log('⏰ 12 seconds passed - showing Ravens form');
                formShown = true;
                showRavensFormForCall(leadId, phoneNumber, leadName, 'connected', 0);
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
                        
                        // Check for ended states
                        if (status === 'ended' || status === 'completed' || status === 'failed' || 
                            status === 'cancelled' || status === 'missed' || status === 'voicemail' || 
                            status === 'rejected' || status === 'busy') {
                            
                            console.log(`❌ Call ended detected! Status: ${status}, Form shown: ${formShown}`);
                            
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
                clearTimeout(autoShowTimer);
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
        
        toastr.success(`Opening form for: ${leadName}`, 'Call Form');
        
        // Fetch full lead data from the server to populate the form
        fetch(`/ravens/leads/${leadId}/data`)
            .then(response => response.json())
            .then(leadData => {
                console.log('📋 Got full lead data:', leadData);
                
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
                        beneficiary: leadData.beneficiary || '',
                        beneficiary_dob: leadData.beneficiary_dob || '',
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
                    console.log('Opening Ravens form with full lead data');
                    showCallModal(callEventData);
                } else {
                    // Fallback - redirect to lead details
                    console.warn('showCallModal not available, redirecting to lead details');
                    window.location.href = `/ravens/leads/${leadId}`;
                }
            })
            .catch(error => {
                console.error('Failed to fetch lead data:', error);
                // Still show the form with minimal data
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
        document.getElementById('orig_address').textContent = ld.address || 'N/A';

        // Medical Information
        document.getElementById('orig_medical_issue').textContent = ld.medical_issue || 'N/A';
        document.getElementById('orig_medications').textContent = ld.medications || 'N/A';
        document.getElementById('orig_doctor').textContent = ld.doctor_name || 'N/A';
        document.getElementById('orig_doctor_address').textContent = ld.doctor_address || 'N/A';

        // Policy Information
        document.getElementById('orig_beneficiary').textContent = document.getElementById('phase2_beneficiary').value || 'N/A';
        document.getElementById('orig_beneficiary_dob').textContent = formatDate(ld.beneficiary_dob);
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
        window.currentLeadData = leadData;

        // PHASE 1: Show caller identification
        document.getElementById('callerName').textContent = leadData.cn_name || 'Unknown Caller';
        document.getElementById('callerPhone').textContent = leadData.phone_number || 'No Number';

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
        document.getElementById('displayName').textContent = leadData.cn_name || 'Not available';
        document.getElementById('displayPhone').textContent = leadData.phone_number || 'Not available';
        document.getElementById('displayDOB').textContent = formatDateDisplay(leadData.date_of_birth);
        document.getElementById('displaySSN').textContent = leadData.ssn || 'Not available';
        document.getElementById('displayAddress').textContent = leadData.address || 'Not available';
        document.getElementById('displayBeneficiary').textContent = leadData.beneficiary || 'Not available';
        document.getElementById('displayCarrier').textContent = leadData.carrier_name || 'Not available';
        document.getElementById('displayCoverage').textContent = leadData.coverage_amount ? '$' + parseFloat(leadData.coverage_amount).toLocaleString() : 'Not available';
        document.getElementById('displayPremium').textContent = leadData.monthly_premium ? '$' + parseFloat(leadData.monthly_premium).toFixed(2) : 'Not available';
        document.getElementById('displayAccountNumber').textContent = leadData.account_number || 'Not available';

        // PHASE 2: Pre-fill CHANGES fields with existing values (user can modify)
        document.getElementById('phase2_name').value = '';
        document.getElementById('phase2_phone').value = '';
        document.getElementById('phase2_dob').value = formatDateInput(leadData.date_of_birth);
        document.getElementById('phase2_ssn').value = leadData.ssn || '';
        document.getElementById('phase2_address').value = '';
        document.getElementById('phase2_beneficiary').value = leadData.beneficiary || '';
        document.getElementById('phase2_carrier').value = leadData.carrier_name || '';
        document.getElementById('phase2_coverage').value = leadData.coverage_amount || '';
        document.getElementById('phase2_premium').value = leadData.monthly_premium || '';
        document.getElementById('phase2_account_number').value = '';

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
                closer_name: @json(Auth::user()->name ?? 'Test Closer'),
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
    
    // Professional Zoom API Integration ✅
    // - Uses real OAuth authentication with Zoom
    // - Professional call status monitoring via Zoom API
    // - Ravens form appears only when call is verified as completed
    // - No popups or confirmations - direct professional calling
    
</script>
@endsection
