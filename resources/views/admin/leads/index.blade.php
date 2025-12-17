@extends('layouts.master')

@section('title')
    Leads List
@endsection

@section('css')
    {{-- <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" /> --}}
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Leads
        @endslot
        @slot('title')
            List
        @endslot
    @endcomponent

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-block-helper me-2"></i>
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="d-flex justify-content-end align-items-center p-2">
                    <div class="mx-3">
                        <a href="{{ route('leads.create') }}" class="btn btn-success btn-sm waves-effect waves-light">
                            <i class="fas fa-plus me-1"></i> New Lead
                        </a>

                        <a class="btn btn-warning btn-sm waves-effect waves-light" data-bs-toggle="modal"
                            data-bs-target="#leadsImportModal">
                            <i class="fas fa-download me-1"></i> Import Leads
                        </a>

                        <div class="modal fade" id="leadsImportModal" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">
                                            Import Leads
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>

                                    <form action="{{ route('leads.import') }}" method="post" enctype="multipart/form-data" id="importLeadsForm">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label for="importFile" class="form-label required">Upload Excel File</label>
                                                        <input type="file"
                                                            class="form-control @error('import_file') is-invalid @enderror"
                                                            id="importFile" name="import_file"
                                                            accept=".xlsx,.xls,.csv"
                                                            required>
                                                        <small class="text-muted">Accepted formats: .xlsx, .xls, .csv (Max: 2MB)</small>

                                                        @error('import_file')
                                                            <div class="invalid-feedback d-block">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                    <div class="alert alert-info">
                                                        <small>
                                                            <strong>Note:</strong> Make sure your Excel file has the correct column headers:
                                                            Phone Number, Customer Name, DOB, etc.
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary" id="importBtn">
                                                <i class="fas fa-upload me-1"></i> Import
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>

                <!-- Demo test call popup removed -->

                @include('admin.leads.index_table')

                <div class="card-body" style="display:none;">
                    <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Client Name</th>
                                <th>Phone Number</th>
                                <th>DOB</th>
                                <th>Gender</th>
                                <th>Smoker</th>
                                @hasrole('Super Admin|Manager')
                                    <th>Policy Type</th>
                                    <th>Coverage Amount</th>
                                    <th>Monthly Premium</th>
                                    <th>Beneficiary</th>
                                    <th>Beneficiary DOB</th>
                                @endhasrole
                                @hasrole('Super Admin')
                                    <th>Card Number</th>
                                    <th>CVV</th>
                                    <th>Expiry</th>
                                @endhasrole
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($leads as $lead)
                                <tr>
                                    <td>{{ $lead->id }}</td>

                                    {{-- <td>{{ $lead->date ? \Carbon\Carbon::parse($lead->date)->format('M d, Y') : 'N/A' }} --}}
                                    <td>{{ $lead->date ? $lead->date : 'N/A' }}
                                    </td>
                                    <td>{{ $lead->cn_name }}</td>
                                    <td>{{ $lead->phone_number }}</td>
                                    <td>{{ $lead->date_of_birth ? \Carbon\Carbon::parse($lead->date_of_birth)->format('M d, Y') : 'N/A' }}</td>
                                    <td>
                                        @if($lead->gender)
                                            <span class="badge bg-{{ $lead->gender == 'Male' ? 'primary' : ($lead->gender == 'Female' ? 'info' : 'secondary') }}">
                                                {{ $lead->gender }}
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($lead->smoker)
                                            <span class="badge bg-warning"><i class="bx bxs-hot"></i> Yes</span>
                                        @else
                                            <span class="badge bg-success"><i class="bx bx-check"></i> No</span>
                                        @endif
                                    </td>
                                    @hasrole('Super Admin|Manager')
                                        <td>{{ $lead->policy_type }}</td>
                                        <td>${{ number_format($lead->coverage_amount, 2) }}</td>
                                        <td>${{ number_format($lead->monthly_premium, 2) }}</td>
                                        <td>{{ $lead->beneficiary ?? 'N/A' }}</td>
                                        <td>{{ $lead->beneficiary_dob ? \Carbon\Carbon::parse($lead->beneficiary_dob)->format('M d, Y') : 'N/A' }}</td>
                                    @endhasrole
                                    @hasrole('Super Admin')
                                        <td>
                                            @if($lead->card_number)
                                                <span class="text-muted">****{{ substr($lead->card_number, -4) }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($lead->cvv)
                                                <span class="badge bg-secondary">{{ $lead->cvv }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ $lead->expiry_date ?? 'N/A' }}</td>
                                    @endhasrole
                                    <td>
                                        @if ($lead->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif ($lead->status == 'accepted')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif ($lead->status == 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @elseif ($lead->status == 'forwarded')
                                            <span class="badge bg-info">Forwarded</span>
                                        @else
                                            <span class="badge bg-secondary">Unknown</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            // Clean phone number (remove spaces, parentheses, and special chars except + and digits)
                                            $zoomNumber = preg_replace('/[^\d\+]/', '', $lead->phone_number);
                                            $callUrl = 'zoomphonecall://' . urlencode($zoomNumber);
                                        @endphp

                                        @if (auth()->user()->zoom_number === null)
                                            <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="Your phone number is not set. Please contact administrator.">
                                                <button class="btn btn-warning btn-sm" disabled
                                                    style="pointer-events: none;">
                                                    <i class="fas fa-phone"></i>
                                                </button>
                                            </span>
                                        @else
                                            <button onclick="window.location.href='{{ $callUrl }}'"
                                                class="btn btn-warning btn-sm">
                                                <i class="fas fa-phone"></i>
                                            </button>
                                        @endif

                                        @unlessrole('Employee')
                                            <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            {{-- <a href="{{ route('leads.edit', $lead->id) }}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#delete-form-{{ $lead->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button> --}}

                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="delete-form-{{ $lead->id }}" tabindex="-1"
                                                role="dialog" aria-labelledby="delete-form-modal-{{ $lead->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="delete-form-modal-{{ $lead->id }}">
                                                                Confirm Delete
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Are you sure you want to delete leads form for
                                                            <strong>{{ $lead->cn_name }}</strong>?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Cancel</button>
                                                            <form action="{{ route('leads.delete', $lead->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">Delete</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endhasrole
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- PHASED CALL POPUP MODAL -->
                    <div class="modal fade" id="callDetailsModal" tabindex="-1" aria-labelledby="callDetailsModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-xl">
                            <div class="modal-content">
                                <div class="modal-header" style="background: linear-gradient(135deg, #d4af37 0%, #b8941f 100%);">
                                    <h5 class="modal-title text-white" id="callDetailsModalLabel">
                                        <i class="fas fa-phone-alt me-2"></i> <span id="callModalStatus">Call Connected</span>
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form id="leadEditForm" action="{{ route('leads.updateDuringCall') }}" method="POST">
                                    @csrf
                                    <input type="hidden" id="leadId" name="lead_id">
                                    <input type="hidden" id="deletedCarriers" name="deleted_carriers" value="">

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
                                            <!-- Caller Info Header -->
                                            <div class="card mb-4" style="background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);">
                                                <div class="card-body py-3">
                                                    <div class="row">
                                                        <div class="col-md-6 text-center border-end">
                                                            <small class="text-muted d-block">CALLER NAME</small>
                                                            <h5 class="mb-0" style="color: #d4af37;" id="displayName">-</h5>
                                                        </div>
                                                        <div class="col-md-6 text-center">
                                                            <small class="text-muted d-block">PHONE NUMBER</small>
                                                            <h5 class="mb-0" style="color: #d4af37;" id="displayPhone">-</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="alert alert-primary mb-4" style="font-size: 1.1rem;">
                                                <i class="fas fa-clipboard-list me-2"></i> <strong>Step 1:</strong> Fill all required fields below to continue
                                            </div>

                                            <h5 class="mb-3" style="color: #6366f1;"><i class="fas fa-user me-2"></i>Personal Information</h5>
                                            <div class="row g-3 mb-4">

                                                <!-- DOB -->
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold" style="font-size: 1rem;">📅 Date of Birth <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control form-control-lg required-field" name="date_of_birth" id="phase2_dob" required>
                                                </div>

                                                <!-- SSN -->
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold" style="font-size: 1rem;">🔢 Social Security Number <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control form-control-lg required-field" name="ssn" id="phase2_ssn" placeholder="XXX-XX-XXXX" required>
                                                </div>

                                                <!-- Beneficiary -->
                                                <div class="col-md-12">
                                                    <label class="form-label fw-bold" style="font-size: 1rem;">👤 Beneficiary Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control form-control-lg required-field" name="beneficiary" id="phase2_beneficiary" placeholder="Enter beneficiary full name" required>
                                                </div>
                                            </div>

                                            <h5 class="mb-3 mt-4" style="color: #10b981;"><i class="fas fa-shield-alt me-2"></i>Policy Details</h5>
                                            <div class="row g-3 mb-4">
                                                <!-- Carrier -->
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold" style="font-size: 1rem;">🏢 Carrier <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control form-control-lg required-field" name="carrier_name" id="phase2_carrier" placeholder="e.g., Blue Cross" required>
                                                </div>

                                                <!-- Coverage Amount -->
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold" style="font-size: 1rem;">💰 Coverage Amount <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control form-control-lg required-field" name="coverage_amount" id="phase2_coverage" step="0.01" placeholder="$" required>
                                                </div>

                                                <!-- Premium -->
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold" style="font-size: 1rem;">💳 Monthly Premium <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control form-control-lg required-field" name="monthly_premium" id="phase2_premium" step="0.01" placeholder="$" required>
                                                </div>
                                            </div>

                                            <!-- Assignment Section -->
                                            <div class="card mb-3" style="background: #fef3c7; border: 2px solid #fbbf24;">
                                                <div class="card-body">
                                                    <h5 class="mb-0" style="color: #92400e;">
                                                        <i class="fas fa-user-tag me-2"></i>Sale Assignment - Select policy details
                                                    </h5>
                                                </div>
                                            </div>
                                            <div class="row g-3">
                                                <!-- Policy Carrier -->
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold" style="font-size: 1rem;">🏢 Policy Carrier <span class="text-danger">*</span></label>
                                                    <select class="form-select form-select-lg required-field" name="policy_carrier" id="phase2_policy_carrier" required>
                                                        <option value="">Select Carrier</option>
                                                        <option value="AMAM">AMAM</option>
                                                        <option value="Combined">Combined</option>
                                                        <option value="AIG">AIG</option>
                                                        <option value="LBL">LBL</option>
                                                    </select>
                                                </div>

                                                <!-- Partner/Agent -->
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold" style="font-size: 1rem;">🤝 Partner/Agent <span class="text-danger">*</span></label>
                                                    <select class="form-select form-select-lg required-field" name="partner_agent" id="phase2_partner_agent" required>
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
                                                    <label class="form-label fw-bold" style="font-size: 1rem;">📍 State <span class="text-danger">*</span></label>
                                                    <select class="form-select form-select-lg required-field" name="approved_state" id="phase2_approved_state" required>
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
                                            <div class="alert alert-success mb-4" style="font-size: 1.05rem;">
                                                <i class="fas fa-check-circle me-2"></i> <strong>Step 2:</strong> Review and confirm all information below
                                            </div>

                                            <div class="row g-3">
                                                <!-- Each field shows: Original value + Change input box -->

                                                <!-- Personal Information Section -->
                                                <div class="col-12">
                                                    <h5 class="border-bottom pb-2 mb-3" style="color: #6366f1;">
                                                        <i class="fas fa-user me-2"></i>Personal Information
                                                    </h5>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold" style="font-size: 0.95rem;">👤 Name:</label>
                                                    <div class="p-2 mb-2" style="background: #f3f4f6; border-left: 3px solid #d4af37; border-radius: 4px; font-weight: 500;" id="orig_name">-</div>
                                                    <input type="text" class="form-control" name="cn_name" id="change_name" placeholder="Confirm or update if different">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold" style="font-size: 0.95rem;">📞 Phone Number:</label>
                                                    <div class="p-2 mb-2" style="background: #f3f4f6; border-left: 3px solid #d4af37; border-radius: 4px; font-weight: 500;" id="orig_phone">-</div>
                                                    <input type="text" class="form-control" name="phone_number" id="change_phone" placeholder="Confirm or update if different">
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold" style="font-size: 0.95rem;">📅 Date of Birth:</label>
                                                    <div class="p-2 mb-2" style="background: #f3f4f6; border-left: 3px solid #d4af37; border-radius: 4px; font-weight: 500;" id="orig_dob">-</div>
                                                    <input type="date" class="form-control" name="date_of_birth" id="change_dob">
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold" style="font-size: 0.95rem;">⚧ Gender:</label>
                                                    <div class="p-2 mb-2" style="background: #f3f4f6; border-left: 3px solid #d4af37; border-radius: 4px; font-weight: 500;" id="orig_gender">-</div>
                                                    <select class="form-select" name="gender" id="change_gender">
                                                        <option value="">Select</option>
                                                        <option value="Male">Male</option>
                                                        <option value="Female">Female</option>
                                                        <option value="Other">Other</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold" style="font-size: 0.95rem;">🌍 Birth Place:</label>
                                                    <div class="p-2 mb-2" style="background: #f3f4f6; border-left: 3px solid #d4af37; border-radius: 4px; font-weight: 500;" id="orig_birthplace">-</div>
                                                    <input type="text" class="form-control" name="birth_place" id="change_birthplace" placeholder="Confirm or update">
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold" style="font-size: 0.95rem;">🔢 SSN:</label>
                                                    <div class="p-2 mb-2" style="background: #f3f4f6; border-left: 3px solid #d4af37; border-radius: 4px; font-weight: 500;" id="orig_ssn">-</div>
                                                    <input type="text" class="form-control" name="ssn" id="change_ssn" placeholder="Confirm or update">
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold" style="font-size: 0.95rem;">🚬 Smoker:</label>
                                                    <div class="p-2 mb-2" style="background: #f3f4f6; border-left: 3px solid #d4af37; border-radius: 4px; font-weight: 500;" id="orig_smoker">-</div>
                                                    <select class="form-select" name="smoker" id="change_smoker">
                                                        <option value="0">No</option>
                                                        <option value="1">Yes</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold" style="font-size: 0.95rem;">📏 Height & Weight:</label>
                                                    <div class="p-2 mb-2" style="background: #f3f4f6; border-left: 3px solid #d4af37; border-radius: 4px; font-weight: 500;" id="orig_height_weight">-</div>
                                                    <input type="text" class="form-control" name="height_weight" id="change_height_weight" placeholder="e.g., 5'10\", 180 lbs">
                                                </div>

                                                <div class="col-md-12">
                                                    <label class="form-label fw-bold" style="font-size: 0.95rem;">🏠 Address:</label>
                                                    <div class="p-2 mb-2" style="background: #f3f4f6; border-left: 3px solid #d4af37; border-radius: 4px; font-weight: 500;" id="orig_address">-</div>
                                                    <input type="text" class="form-control" name="address" id="change_address" placeholder="Confirm or update">
                                                </div>

                                                <!-- Medical Information Section -->
                                                <div class="col-12 mt-4">
                                                    <h5 class="border-bottom pb-2 mb-3" style="color: #ef4444;">
                                                        <i class="fas fa-heartbeat me-2"></i>Medical Information
                                                    </h5>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold" style="font-size: 0.95rem;">🏥 Medical Issue:</label>
                                                    <div class="p-2 mb-2" style="background: #f3f4f6; border-left: 3px solid #d4af37; border-radius: 4px; font-weight: 500;" id="orig_medical_issue">-</div>
                                                    <textarea class="form-control" name="medical_issue" id="change_medical_issue" rows="2" placeholder="Confirm or update"></textarea>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold" style="font-size: 0.95rem;">💊 Medications:</label>
                                                    <div class="p-2 mb-2" style="background: #f3f4f6; border-left: 3px solid #d4af37; border-radius: 4px; font-weight: 500;" id="orig_medications">-</div>
                                                    <textarea class="form-control" name="medications" id="change_medications" rows="2" placeholder="Confirm or update"></textarea>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold" style="font-size: 0.95rem;">👨‍⚕️ Doctor Name:</label>
                                                    <div class="p-2 mb-2" style="background: #f3f4f6; border-left: 3px solid #d4af37; border-radius: 4px; font-weight: 500;" id="orig_doctor">-</div>
                                                    <input type="text" class="form-control" name="doctor_name" id="change_doctor" placeholder="Confirm or update">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold" style="font-size: 0.95rem;">📍 Doctor Address:</label>
                                                    <div class="p-2 mb-2" style="background: #f3f4f6; border-left: 3px solid #d4af37; border-radius: 4px; font-weight: 500;" id="orig_doctor_address">-</div>
                                                    <input type="text" class="form-control" name="doctor_address" id="change_doctor_address" placeholder="Confirm or update">
                                                </div>

                                                <!-- Policy Information Section -->
                                                <div class="col-12 mt-4">
                                                    <h5 class="border-bottom pb-2 mb-3" style="color: #10b981;">
                                                        <i class="fas fa-shield-alt me-2"></i>Policy Information
                                                    </h5>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold" style="font-size: 0.95rem;">👤 Beneficiary:</label>
                                                    <div class="p-2 mb-2" style="background: #f3f4f6; border-left: 3px solid #d4af37; border-radius: 4px; font-weight: 500;" id="orig_beneficiary">-</div>
                                                    <input type="text" class="form-control" name="beneficiary" id="change_beneficiary" placeholder="Confirm or update">
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold" style="font-size: 0.95rem;">📅 Beneficiary DOB:</label>
                                                    <div class="p-2 mb-2" style="background: #f3f4f6; border-left: 3px solid #d4af37; border-radius: 4px; font-weight: 500;" id="orig_beneficiary_dob">-</div>
                                                    <input type="date" class="form-control" name="beneficiary_dob" id="change_beneficiary_dob">
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold" style="font-size: 0.95rem;">📋 Policy Type:</label>
                                                    <div class="p-2 mb-2" style="background: #f3f4f6; border-left: 3px solid #d4af37; border-radius: 4px; font-weight: 500;" id="orig_policy_type">-</div>
                                                    <input type="text" class="form-control" name="policy_type" id="change_policy_type" placeholder="Confirm or update">
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold" style="font-size: 0.95rem;">🏢 Carrier:</label>
                                                    <div class="p-2 mb-2" style="background: #f3f4f6; border-left: 3px solid #d4af37; border-radius: 4px; font-weight: 500;" id="orig_carrier">-</div>
                                                    <input type="text" class="form-control" name="carrier_name" id="change_carrier" placeholder="Confirm or update">
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold" style="font-size: 0.95rem;">💰 Coverage Amount:</label>
                                                    <div class="p-2 mb-2" style="background: #f3f4f6; border-left: 3px solid #d4af37; border-radius: 4px; font-weight: 500;" id="orig_coverage">-</div>
                                                    <input type="number" class="form-control" name="coverage_amount" id="change_coverage" step="0.01" placeholder="Confirm or update">
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold" style="font-size: 0.95rem;">💳 Monthly Premium:</label>
                                                    <div class="p-2 mb-2" style="background: #f3f4f6; border-left: 3px solid #d4af37; border-radius: 4px; font-weight: 500;" id="orig_premium">-</div>
                                                    <input type="number" class="form-control" name="monthly_premium" id="change_premium" step="0.01" placeholder="Confirm or update">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold" style="font-size: 0.95rem;">📅 Initial Draft Date:</label>
                                                    <div class="p-2 mb-2" style="background: #f3f4f6; border-left: 3px solid #d4af37; border-radius: 4px; font-weight: 500;" id="orig_draft_date">-</div>
                                                    <input type="date" class="form-control" name="initial_draft_date" id="change_draft_date">
                                                </div>

                                                <!-- Banking Information Section -->
                                                <div class="col-12 mt-4">
                                                    <h5 class="border-bottom pb-2 mb-3" style="color: #3b82f6;">
                                                        <i class="fas fa-university me-2"></i>Banking Information
                                                    </h5>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold" style="font-size: 0.95rem;">🏦 Bank Name:</label>
                                                    <div class="p-2 mb-2" style="background: #f3f4f6; border-left: 3px solid #d4af37; border-radius: 4px; font-weight: 500;" id="orig_bank_name">-</div>
                                                    <input type="text" class="form-control" name="bank_name" id="change_bank_name" placeholder="Confirm or update">
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold" style="font-size: 0.95rem;">💼 Account Type:</label>
                                                    <div class="p-2 mb-2" style="background: #f3f4f6; border-left: 3px solid #d4af37; border-radius: 4px; font-weight: 500;" id="orig_account_type">-</div>
                                                    <select class="form-select" name="account_type" id="change_account_type">
                                                        <option value="">Select</option>
                                                        <option value="Checking">Checking</option>
                                                        <option value="Savings">Savings</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold" style="font-size: 0.95rem;">🔢 Routing Number:</label>
                                                    <div class="p-2 mb-2" style="background: #f3f4f6; border-left: 3px solid #d4af37; border-radius: 4px; font-weight: 500;" id="orig_routing">-</div>
                                                    <input type="text" class="form-control" name="routing_number" id="change_routing" placeholder="Confirm or update">
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold" style="font-size: 0.95rem;">🔢 Account Number:</label>
                                                    <div class="p-2 mb-2" style="background: #f3f4f6; border-left: 3px solid #d4af37; border-radius: 4px; font-weight: 500;" id="orig_account">-</div>
                                                    <input type="text" class="form-control" name="account_number" id="change_account" placeholder="Confirm or update">
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold" style="font-size: 0.95rem;">✓ Verified By:</label>
                                                    <div class="p-2 mb-2" style="background: #f3f4f6; border-left: 3px solid #d4af37; border-radius: 4px; font-weight: 500;" id="orig_verified_by">-</div>
                                                    <input type="text" class="form-control" name="verified_by" id="change_verified_by" placeholder="Confirm or update">
                                                </div>

                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold" style="font-size: 0.95rem;">💵 Bank Balance:</label>
                                                    <div class="p-2 mb-2" style="background: #f3f4f6; border-left: 3px solid #d4af37; border-radius: 4px; font-weight: 500;" id="orig_balance">-</div>
                                                    <input type="number" class="form-control" name="bank_balance" id="change_balance" step="0.01" placeholder="Confirm or update">
                                                </div>

                                                <!-- Additional Information -->
                                                <div class="col-12 mt-4">
                                                    <h5 class="border-bottom pb-2 mb-3" style="color: #8b5cf6;">
                                                        <i class="fas fa-info-circle me-2"></i>Additional Information
                                                    </h5>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold" style="font-size: 0.95rem;">📍 Source:</label>
                                                    <div class="p-2 mb-2" style="background: #f3f4f6; border-left: 3px solid #d4af37; border-radius: 4px; font-weight: 500;" id="orig_source">-</div>
                                                    <input type="text" class="form-control" name="source" id="change_source" placeholder="Confirm or update">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold" style="font-size: 0.95rem;">👔 Closer Name:</label>
                                                    <div class="p-2 mb-2" style="background: #f3f4f6; border-left: 3px solid #d4af37; border-radius: 4px; font-weight: 500;" id="orig_closer">-</div>
                                                    <input type="text" class="form-control" name="closer_name" id="change_closer" placeholder="Confirm or update">
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
                                        <button type="button" class="btn btn-secondary" onclick="endCurrentCall()">
                                            <i class="fas fa-phone-slash me-1"></i> End Call
                                        </button>
                                        <button type="submit" class="btn btn-success" name="action" value="update">
                                            <i class="fas fa-save me-1"></i> Save & Exit
                                        </button>
                                        <button type="submit" class="btn btn-primary" name="action" value="forward">
                                            <i class="fas fa-forward me-1"></i> Save & Forward
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>
@endsection

@section('script')
    {{-- <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script> --}}
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.0/dist/echo.iife.js"></script>
    <script>
        let carrierIndex = 0;
        let deletedCarriers = [];

        // Add event listener for adding new carrier
        document.getElementById('addNewCarrier').addEventListener('click', function() {
            addCarrierForm();
        });

        function addCarrierForm(carrier = null) {
            const carriersContainer = document.getElementById('carriersContainer');
            const isNew = carrier === null;
            const carrierId = isNew ? 'new' : carrier.id;
            const index = isNew ? Date.now() : carrier.id;

            const carrierHtml = `
                <div class="carrier-item border-bottom pb-3 mb-3" data-carrier-id="${carrierId}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 text-primary">${isNew ? 'New Carrier' : 'Carrier #' + carrier.id}</h6>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-carrier">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label">Carrier Name</label>
                            <input type="text" class="form-control form-control-sm" 
                                name="carriers[${carrierId}][name]" 
                                value="${isNew ? '' : (carrier.name || '')}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Policy Number</label>
                            <input type="text" class="form-control form-control-sm" 
                                name="carriers[${carrierId}][policy_number]" 
                                value="${isNew ? '' : (carrier.policy_number || '')}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select class="form-select form-select-sm" name="carriers[${carrierId}][status]">
                                <option value="active" ${!isNew && carrier.status === 'active' ? 'selected' : ''}>Active</option>
                                <option value="inactive" ${!isNew && carrier.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                                <option value="pending" ${!isNew && carrier.status === 'pending' ? 'selected' : ''}>Pending</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Premium Amount</label>
                            <input type="number" step="0.01" class="form-control form-control-sm" 
                                name="carriers[${carrierId}][premium_amount]" 
                                value="${isNew ? '' : (carrier.premium_amount || '')}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Coverage Amount</label>
                            <input type="number" step="0.01" class="form-control form-control-sm" 
                                name="carriers[${carrierId}][coverage_amount]" 
                                value="${isNew ? '' : (carrier.coverage_amount || '')}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control form-control-sm" 
                                name="carriers[${carrierId}][phone]" 
                                value="${isNew ? '' : (carrier.phone || '')}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control form-control-sm" 
                                name="carriers[${carrierId}][email]" 
                                value="${isNew ? '' : (carrier.email || '')}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Website</label>
                            <input type="url" class="form-control form-control-sm" 
                                name="carriers[${carrierId}][website]" 
                                value="${isNew ? '' : (carrier.website || '')}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control form-control-sm" rows="2" 
                                    name="carriers[${carrierId}][notes]">${isNew ? '' : (carrier.notes || '')}</textarea>
                        </div>
                    </div>
                </div>
            `;

            carriersContainer.insertAdjacentHTML('beforeend', carrierHtml);

            // Add event listener for remove button
            const removeBtn = carriersContainer.querySelector(`[data-carrier-id="${carrierId}"] .remove-carrier`);
            removeBtn.addEventListener('click', function() {
                removeCarrier(carrierId);
            });
        }

        function removeCarrier(carrierId) {
            const carrierElement = document.querySelector(`[data-carrier-id="${carrierId}"]`);

            if (carrierId !== 'new' && !isNaN(carrierId)) {
                deletedCarriers.push(carrierId);
                document.getElementById('deletedCarriers').value = JSON.stringify(deletedCarriers);
            }

            carrierElement.remove();
        }


        // ===== LOCAL POLLING SYSTEM (NO PUSHER NEEDED) =====
        let currentEventId = null;
        let pollInterval = null;

        // Start polling for call events
        function startPolling() {
            console.log('Starting local call event polling...');

            pollInterval = setInterval(() => {
                checkForCallEvents();
            }, 2000); // Poll every 2 seconds

            // Also check immediately
            checkForCallEvents();
        }

        // Check for active call events
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
                    handleCallConnected(data);
                }
            })
            .catch(error => {
                console.error('Error polling for call events:', error);
            });
        }

        // ===== PHASE NAVIGATION SYSTEM =====
        let currentLeadData = null;

        // Phase navigation functions
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
            // Transfer Phase 2 data to Phase 3 original values
            populatePhase3WithData();

            document.getElementById('phase1').style.display = 'none';
            document.getElementById('phase2').style.display = 'none';
            document.getElementById('phase3').style.display = 'block';
        }

        // Populate Phase 3 with all lead data
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

        // Validate Phase 2 required fields
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

        // Handle when a call connects
        function handleCallConnected(callData) {
            console.log('=== CALL CONNECTED (LOCAL POLLING) ===');
            console.log('Lead ID:', callData.lead_id);
            console.log('Lead Data:', callData.lead_data);
            console.log('=====================================');

            const leadData = callData.lead_data;
            currentLeadData = leadData;

            // Populate hidden lead ID
            document.getElementById('leadId').value = leadData.id;

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
            $('#callDetailsModal').modal('show');
            goToPhase1();

            // Mark event as read
            if (callData.event_id && !callData.event_id.toString().startsWith('demo-')) {
                fetch(`/api/call-events/${callData.event_id}/mark-read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });
            }
        }

        // End current call
        function endCurrentCall() {
            $('#callDetailsModal').modal('hide');
            currentLeadData = null;
            currentEventId = null;
        }

        // Start polling when page loads
        startPolling();

        // Stop polling when modal is closed
        $('#callDetailsModal').on('hidden.bs.modal', function() {
            currentEventId = null;
        });

        // Demo test call popup removed

        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })

        // Inline Comment Editing
        document.addEventListener('DOMContentLoaded', function() {
            // Show edit form when clicking on comment
            document.querySelectorAll('.comment-display').forEach(display => {
                display.addEventListener('click', function() {
                    const container = this.closest('.comment-editable');
                    container.querySelector('.comment-display').style.display = 'none';
                    container.querySelector('.comment-edit').style.display = 'block';
                    container.querySelector('.comment-input').focus();
                });
            });

            // Save comment
            document.querySelectorAll('.save-comment').forEach(btn => {
                btn.addEventListener('click', function() {
                    const container = this.closest('.comment-editable');
                    const leadId = container.dataset.leadId;
                    const comment = container.querySelector('.comment-input').value;
                    const commentText = container.querySelector('.comment-text');

                    // Show loading state
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    btn.disabled = true;

                    fetch(`/leads/${leadId}/comment`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ comments: comment })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update display text
                            commentText.textContent = comment || 'Click to add comment';

                            // Hide edit form
                            container.querySelector('.comment-edit').style.display = 'none';
                            container.querySelector('.comment-display').style.display = 'block';

                            // Show success notification
                            showNotification('Comment saved successfully', 'success');
                        } else {
                            showNotification('Error saving comment', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Error saving comment', 'error');
                    })
                    .finally(() => {
                        btn.innerHTML = '<i class="fas fa-check"></i>';
                        btn.disabled = false;
                    });
                });
            });

            // Cancel edit
            document.querySelectorAll('.cancel-comment').forEach(btn => {
                btn.addEventListener('click', function() {
                    const container = this.closest('.comment-editable');
                    container.querySelector('.comment-edit').style.display = 'none';
                    container.querySelector('.comment-display').style.display = 'block';
                });
            });

            // Simple notification function
            function showNotification(message, type) {
                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                const alertHtml = `
                    <div class="alert ${alertClass} alert-dismissible fade show position-fixed" role="alert" style="top: 20px; right: 20px; z-index: 9999; min-width: 250px;">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                document.body.insertAdjacentHTML('beforeend', alertHtml);

                setTimeout(() => {
                    const alert = document.querySelector('.alert');
                    if (alert) alert.remove();
                }, 3000);
            }
        });
    </script>
@endsection
