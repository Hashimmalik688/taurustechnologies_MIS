@extends('layouts.master')

@section('title')
    Incomplete Issuance - Details
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('retention.incomplete') }}">Retention</a>
        @endslot
        @slot('title')
            Incomplete Details
        @endslot
    @endcomponent

    <div class="row mb-3">
        <div class="col-12">
            <h2 class="text-gold fw-bold">
                <i class="mdi mdi-file-document me-2"></i>{{ $lead->cn_name }} - Complete Details
            </h2>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="card-title mb-0 text-gold fw-semibold">
                        <i class="mdi mdi-account me-2"></i>Customer Information
                    </h5>
                    <a href="{{ route('retention.incomplete') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bx bx-arrow-back"></i> Back
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> {{ $lead->cn_name }}</p>
                            <p><strong>Phone:</strong> {{ $lead->phone_number }}</p>
                            <p><strong>Secondary Phone:</strong> {{ $lead->secondary_phone_number ?? 'N/A' }}</p>
                            <p><strong>SSN:</strong> {{ $lead->ssn ? '****'.substr($lead->ssn, -4) : 'N/A' }}</p>
                            <p><strong>Date of Birth:</strong> {{ $lead->date_of_birth ? \Carbon\Carbon::parse($lead->date_of_birth)->format('M d, Y') : 'N/A' }}</p>
                            <p><strong>State:</strong> {{ $lead->state ?? 'N/A' }}</p>
                            <p><strong>Zip Code:</strong> {{ $lead->zip_code ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Address:</strong> {{ $lead->address ?? 'N/A' }}</p>
                            <p><strong>Gender:</strong> {{ $lead->gender ?? 'N/A' }}</p>
                            <p><strong>Smoker:</strong> {{ $lead->smoker ?? 'N/A' }}</p>
                            <p><strong>Height/Weight:</strong> {{ $lead->height_weight ?? 'N/A' }}</p>
                            <p><strong>Emergency Contact:</strong> {{ $lead->emergency_contact ?? 'N/A' }}</p>
                            <p><strong>Beneficiary:</strong> {{ $lead->beneficiary ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 text-gold fw-semibold">
                        <i class="mdi mdi-briefcase me-2"></i>Policy Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Carrier:</strong> {{ $lead->carrier_name ?? 'N/A' }}</p>
                            <p><strong>Policy Number:</strong> {{ $lead->policy_number ?? 'N/A' }}</p>
                            <p><strong>Policy Type:</strong> {{ $lead->policy_type ?? 'N/A' }}</p>
                            <p><strong>Coverage Amount:</strong> <span class="text-gold fw-semibold">${{ number_format($lead->coverage_amount ?? 0, 2) }}</span></p>
                            <p><strong>Monthly Premium:</strong> <span class="text-gold fw-semibold">${{ number_format($lead->monthly_premium ?? 0, 2) }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Sale Date:</strong> {{ $lead->sale_date ? \Carbon\Carbon::parse($lead->sale_date)->format('M d, Y') : 'N/A' }}</p>
                            <p><strong>Initial Draft Date:</strong> {{ $lead->initial_draft_date ? \Carbon\Carbon::parse($lead->initial_draft_date)->format('M d, Y') : 'N/A' }}</p>
                            <p><strong>Future Draft Date:</strong> {{ $lead->future_draft_date ? \Carbon\Carbon::parse($lead->future_draft_date)->format('M d, Y') : 'N/A' }}</p>
                            <p><strong>Closer:</strong> {{ $lead->closer_name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 text-gold fw-semibold">
                        <i class="mdi mdi-bank me-2"></i>Bank Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Bank Name:</strong> {{ $lead->bank_name ?? 'N/A' }}</p>
                            <p><strong>Account Title:</strong> {{ $lead->account_title ?? 'N/A' }}</p>
                            <p><strong>Account Type:</strong> {{ $lead->account_type ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Routing Number:</strong> {{ $lead->routing_number ? '****'.substr($lead->routing_number, -4) : 'N/A' }}</p>
                            <p><strong>Account Number:</strong> {{ $lead->account_number ? '****'.substr($lead->account_number, -4) : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 text-gold fw-semibold">
                        <i class="mdi mdi-clipboard-list me-2"></i>Approval Status & Issuance
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>QA Status:</strong> 
                                @if($lead->qa_status)
                                    <span class="badge bg-info">{{ $lead->qa_status }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </p>
                            <p><strong>QA Reason:</strong> {{ $lead->qa_reason ?? '—' }}</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Manager Status:</strong>
                                @if($lead->manager_status)
                                    <span class="badge bg-warning">{{ $lead->manager_status }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </p>
                            <p><strong>Manager Reason:</strong> {{ $lead->manager_reason ?? '—' }}</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Issuance Status:</strong>
                                <span class="badge bg-danger">{{ $lead->issuance_status ?? 'N/A' }}</span>
                            </p>
                            <p><strong>Issuance Reason:</strong> {{ $lead->issuance_reason ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0 text-gold fw-semibold">
                        <i class="mdi mdi-history me-2"></i>Disposition Information
                    </h5>
                </div>
                <div class="card-body">
                    @if($lead->issuance_disposition)
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Disposition Channel:</strong>
                                    @php
                                        $badgeClass = match($lead->issuance_disposition) {
                                            'Via Portal' => 'bg-success',
                                            'Via Email' => 'bg-info',
                                            'By Carrier' => 'bg-warning',
                                            'By Bank' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $lead->issuance_disposition }}</span>
                                </p>
                                <p><strong>Disposition Date:</strong> {{ $lead->issuance_disposition_date ? \Carbon\Carbon::parse($lead->issuance_disposition_date)->format('M d, Y h:i A') : 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Disposition Officer:</strong> {{ $lead->dispositionOfficer->name ?? 'N/A' }}</p>
                                <p><strong>Other Insurances Found:</strong>
                                    @if($lead->has_other_insurances)
                                        <span class="badge bg-warning">Yes</span>
                                    @else
                                        <span class="badge bg-success">No</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <p><strong>Disposition Notes:</strong></p>
                            <div class="alert alert-light p-3">
                                {{ $lead->issuance_reason ?? 'No notes recorded' }}
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="mdi mdi-information me-2"></i>
                            <strong>No disposition recorded yet.</strong> This lead is awaiting disposition assignment.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
