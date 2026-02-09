@extends('layouts.master')

@section('title')
    View Lead
@endsection

@section('css')
    <style>
        :root {
            --gold: #d4af37;
            --gold-light: #f5e6c8;
            --gold-dark: #b8941f;
            --gold-bright: #f0d896;
        }

        /* Lead Header */
        .lead-header {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 24px rgba(212, 175, 55, 0.3);
            color: #1a1a1a;
        }

        .lead-header h2 {
            color: #1a1a1a;
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-size: 1.75rem;
        }

        .lead-header .lead-info {
            display: flex;
            gap: 2rem;
            align-items: center;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .lead-header .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1rem;
            color: #2d2d2d;
        }

        .lead-header .info-item i {
            font-size: 1.2rem;
            opacity: 0.8;
        }

        .lead-header .lead-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .btn-call {
            background: #28a745;
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }

        .btn-call:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.5);
            color: white;
        }

        .btn-back {
            background: rgba(0, 0, 0, 0.2);
            border: none;
            color: #1a1a1a;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: rgba(0, 0, 0, 0.3);
            color: #1a1a1a;
        }

        /* Card Styling */
        .info-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(212, 175, 55, 0.1);
        }

        .info-card:hover {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .card-header-gold {
            background: linear-gradient(90deg, rgba(212, 175, 55, 0.1) 0%, rgba(212, 175, 55, 0.05) 100%);
            border-bottom: 2px solid var(--gold);
            padding: 1rem 1.5rem;
            border-radius: 12px 12px 0 0;
        }

        .card-header-gold h5 {
            color: var(--gold-dark);
            font-weight: 700;
            font-size: 1.1rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-header-gold i {
            font-size: 1.2rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Info Fields */
        .info-row {
            margin-bottom: 1.25rem;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            font-size: 0.75rem;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.4rem;
        }

        .info-value {
            font-size: 0.95rem;
            font-weight: 500;
            color: #2d3436;
            word-wrap: break-word;
        }

        .info-value.empty {
            color: #95a5a6;
            font-style: italic;
        }

        /* Badges */
        .badge-gold {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            color: #1a1a1a;
            font-weight: 600;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.85rem;
        }

        .badge-status {
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .badge-status.active {
            background: #28a745;
            color: white;
        }

        .badge-status.pending {
            background: #ffc107;
            color: #1a1a1a;
        }

        .badge-status.inactive {
            background: #6c757d;
            color: white;
        }

        .badge-gender-male {
            background: #007bff;
            color: white;
        }

        .badge-gender-female {
            background: #e83e8c;
            color: white;
        }

        .badge-smoker-yes {
            background: #dc3545;
            color: white;
        }

        .badge-smoker-no {
            background: #28a745;
            color: white;
        }

        .badge-coverage {
            background: #17a2b8;
            color: white;
            font-weight: 600;
        }

        .badge-premium {
            background: #6f42c1;
            color: white;
            font-weight: 600;
        }

        /* Masked Data */
        .masked-data {
            font-family: 'Courier New', monospace;
            background: rgba(212, 175, 55, 0.1);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .lead-header {
                padding: 1.5rem;
            }

            .lead-header h2 {
                font-size: 1.5rem;
            }

            .lead-header .lead-info {
                gap: 1rem;
            }

            .lead-header .lead-actions {
                flex-direction: column;
                gap: 0.75rem;
            }

            .lead-header .lead-actions .btn {
                width: 100%;
            }
        }

        /* Alert Styling */
        .alert {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Live & Health Checklist */
        .pipeline-checklist {
            /* Normal layout flow - no sticky/fixed positioning */
        }

        .checklist-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border-left: 3px solid #e9ecef;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
            border-radius: 0 8px 8px 0;
        }

        .checklist-item.completed {
            border-left-color: var(--gold);
            background: rgba(212, 175, 55, 0.05);
        }

        .checklist-item.current {
            border-left-color: #28a745;
            background: rgba(40, 167, 69, 0.05);
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.15);
        }

        .checklist-checkbox {
            width: 24px;
            height: 24px;
            border: 2px solid #dee2e6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .checklist-item.completed .checklist-checkbox {
            background: var(--gold);
            border-color: var(--gold);
        }

        .checklist-item.current .checklist-checkbox {
            background: #28a745;
            border-color: #28a745;
            animation: pulse 2s infinite;
        }

        .checklist-checkbox i {
            color: white;
            font-size: 14px;
        }

        .checklist-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: #6c757d;
        }

        .checklist-item.completed .checklist-label {
            color: var(--gold-dark);
        }

        .checklist-item.current .checklist-label {
            color: #28a745;
            font-weight: 600;
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
            }
            50% {
                box-shadow: 0 0 0 6px rgba(40, 167, 69, 0);
            }
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Leads
        @endslot
        @slot('title')
            View Lead
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

    <!-- Lead Header -->
    <div class="lead-header">
        <h2>{{ $insurance->cn_name ?? 'N/A' }}</h2>
        <div class="lead-info">
            <div class="info-item">
                <i class="mdi mdi-phone"></i>
                <strong>{{ $insurance->phone_number ?? 'N/A' }}</strong>
            </div>
            <div class="info-item">
                <i class="mdi mdi-badge-account"></i>
                <strong>Lead #{{ $insurance->id }}</strong>
            </div>
            <div class="info-item">
                <i class="mdi mdi-calendar"></i>
                <strong>{{ $insurance->created_at ? $insurance->created_at->format('M d, Y') : 'N/A' }}</strong>
            </div>
        </div>
        <div class="lead-actions">
            @if(Auth::user()->zoom_number && $insurance->phone_number)
                <button type="button" class="btn btn-call" onclick="makeZoomCall()">
                    <i class="mdi mdi-phone me-2"></i>Call Lead
                </button>
            @endif
            <a href="{{ route('sales.prettyPrint', $insurance->id) }}" class="btn btn-success" title="Pretty Print" target="_blank">
                <i class="mdi mdi-printer me-2"></i>Pretty Print
            </a>
            <a href="{{ route('leads.index') }}" class="btn btn-back">
                <i class="mdi mdi-arrow-left me-2"></i>Back to Leads
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-6">
            <!-- Personal Information -->
            <div class="info-card">
                <div class="card-header-gold">
                    <h5><i class="mdi mdi-account"></i>Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Full Name</div>
                            <div class="info-value {{ $insurance->cn_name ? '' : 'empty' }}">
                                {{ $insurance->cn_name ?? 'Not provided' }}
                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Primary Phone</div>
                            <div class="info-value {{ $insurance->phone_number ? '' : 'empty' }}">
                                {{ $insurance->phone_number ?? 'Not provided' }}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Secondary Phone</div>
                            <div class="info-value {{ $insurance->secondary_phone_number ? '' : 'empty' }}">
                                {{ $insurance->secondary_phone_number ?? 'Not provided' }}
                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">State / Zip</div>
                            <div class="info-value {{ ($insurance->state || $insurance->zip_code) ? '' : 'empty' }}">
                                {{ $insurance->state ?? '—' }} {{ $insurance->zip_code ?? '—' }}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Date of Birth</div>
                            <div class="info-value {{ $insurance->date_of_birth ? '' : 'empty' }}">
                                {{ $insurance->date_of_birth ? \Carbon\Carbon::parse($insurance->date_of_birth)->format('M d, Y') : 'Not provided' }}
                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Gender</div>
                            <div class="info-value">
                                @if($insurance->gender)
                                    <span class="badge badge-status badge-gender-{{ strtolower($insurance->gender) }}">
                                        {{ $insurance->gender }}
                                    </span>
                                @else
                                    <span class="empty">Not provided</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Birth Place</div>
                            <div class="info-value {{ $insurance->birth_place ? '' : 'empty' }}">
                                {{ $insurance->birth_place ?? 'Not provided' }}
                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Height & Weight</div>
                            <div class="info-value {{ $insurance->height_weight ? '' : 'empty' }}">
                                {{ $insurance->height_weight ?? 'Not provided' }}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Smoker</div>
                            <div class="info-value">
                                @if($insurance->smoker !== null)
                                    <span class="badge badge-status badge-smoker-{{ $insurance->smoker ? 'yes' : 'no' }}">
                                        {{ $insurance->smoker ? 'Yes' : 'No' }}
                                    </span>
                                @else
                                    <span class="empty">Not provided</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Driving License</div>
                            <div class="info-value {{ $insurance->driving_license ? '' : 'empty' }}">
                                {{ $insurance->driving_license ?? 'Not provided' }}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">SSN</div>
                            <div class="info-value {{ $insurance->ssn ? '' : 'empty' }}">
                                {{ $insurance->ssn ?? 'Not provided' }}
                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Address</div>
                            <div class="info-value {{ $insurance->address ? '' : 'empty' }}">
                                {{ $insurance->address ?? 'Not provided' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical Information -->
            <div class="info-card">
                <div class="card-header-gold">
                    <h5><i class="mdi mdi-hospital-box"></i>Medical Information</h5>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <div class="info-label">Medical Issues</div>
                        <div class="info-value {{ $insurance->medical_issue ? '' : 'empty' }}">
                            {{ $insurance->medical_issue ?? 'Not provided' }}
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Medications</div>
                        <div class="info-value {{ $insurance->medications ? '' : 'empty' }}">
                            {{ $insurance->medications ?? 'Not provided' }}
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Primary Care Physician</div>
                        <div class="info-value {{ $insurance->doctor_name ? '' : 'empty' }}">
                            {{ $insurance->doctor_name ?? 'Not provided' }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Doctor Phone</div>
                            <div class="info-value {{ $insurance->doctor_number ? '' : 'empty' }}">
                                {{ $insurance->doctor_number ?? 'Not provided' }}
                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Doctor Address</div>
                            <div class="info-value {{ $insurance->doctor_address ? '' : 'empty' }}">
                                {{ $insurance->doctor_address ?? 'Not provided' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Policy Information -->
            <div class="info-card">
                <div class="card-header-gold">
                    <h5><i class="mdi mdi-shield-check"></i>Policy Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Status</div>
                            <div class="info-value">
                                @if($insurance->status)
                                    <span class="badge badge-status {{ strtolower($insurance->status) }}">
                                        {{ ucfirst($insurance->status) }}
                                    </span>
                                @else
                                    <span class="empty">Not provided</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Policy Type</div>
                            <div class="info-value {{ $insurance->policy_type ? '' : 'empty' }}">
                                {{ $insurance->policy_type ?? 'Not provided' }}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Carrier Name</div>
                            <div class="info-value {{ $insurance->carrier_name ? '' : 'empty' }}">
                                {{ $insurance->carrier_name ?? 'Not provided' }}
                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Coverage Amount</div>
                            <div class="info-value">
                                @if($insurance->coverage_amount)
                                    <span class="badge badge-coverage">
                                        ${{ number_format($insurance->coverage_amount, 0) }}
                                    </span>
                                @else
                                    <span class="empty">Not provided</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Monthly Premium</div>
                            <div class="info-value">
                                @if($insurance->monthly_premium)
                                    <span class="badge badge-premium">
                                        ${{ number_format($insurance->monthly_premium, 2) }}/mo
                                    </span>
                                @else
                                    <span class="empty">Not provided</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Initial Draft Date</div>
                            <div class="info-value {{ $insurance->initial_draft_date ? '' : 'empty' }}">
                                {{ $insurance->initial_draft_date ? \Carbon\Carbon::parse($insurance->initial_draft_date)->format('M d, Y') : 'Not provided' }}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Future Draft Date</div>
                            <div class="info-value {{ $insurance->future_draft_date ? '' : 'empty' }}">
                                {{ $insurance->future_draft_date ? \Carbon\Carbon::parse($insurance->future_draft_date)->format('M d, Y') : 'Not provided' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Beneficiary Information -->
            <div class="info-card">
                <div class="card-header-gold">
                    <h5><i class="mdi mdi-account-heart"></i>Beneficiary Information</h5>
                </div>
                <div class="card-body">
                    @php
                        $beneficiaries = $insurance->beneficiaries ?? [];
                        // Fallback to old fields if no beneficiaries array
                        if (empty($beneficiaries) && ($insurance->beneficiary || $insurance->beneficiary_dob)) {
                            $beneficiaries = [[
                                'name' => $insurance->beneficiary ?? '',
                                'dob' => $insurance->beneficiary_dob ?? '',
                                'relation' => ''
                            ]];
                        }
                    @endphp
                    @if(!empty($beneficiaries))
                        @foreach($beneficiaries as $index => $beneficiary)
                            <div class="row mb-3 pb-3 {{ $loop->last ? '' : 'border-bottom' }}">
                                <div class="col-12 mb-2">
                                    <h6 class="text-primary mb-2"><i class="bx bx-user"></i> Beneficiary {{ count($beneficiaries) > 1 ? ($index + 1) : '' }}</h6>
                                </div>
                                <div class="col-md-4 info-row">
                                    <div class="info-label">Name</div>
                                    <div class="info-value {{ !empty($beneficiary['name']) ? '' : 'empty' }}">
                                        {{ $beneficiary['name'] ?? 'Not provided' }}
                                    </div>
                                </div>
                                <div class="col-md-4 info-row">
                                    <div class="info-label">Relation</div>
                                    <div class="info-value {{ !empty($beneficiary['relation']) ? '' : 'empty' }}">
                                        {{ $beneficiary['relation'] ?? 'Not provided' }}
                                    </div>
                                </div>
                                <div class="col-md-4 info-row">
                                    <div class="info-label">Date of Birth</div>
                                    <div class="info-value {{ !empty($beneficiary['dob']) ? '' : 'empty' }}">
                                        {{ !empty($beneficiary['dob']) ? \Carbon\Carbon::parse($beneficiary['dob'])->format('M d, Y') : 'Not provided' }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="row">
                            <div class="col-12 info-row">
                                <div class="info-value empty">No beneficiaries added</div>
                            </div>
                        </div>
                    @endif
                    <div class="row mt-3">
                        <div class="col-md-12 info-row">
                            <div class="info-label">Emergency Contact</div>
                            <div class="info-value {{ $insurance->emergency_contact ? '' : 'empty' }}">
                                {{ $insurance->emergency_contact ?? 'Not provided' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Follow Up Schedule -->
            <div class="info-card">
                <div class="card-header-gold">
                    <h5><i class="mdi mdi-calendar-clock"></i>Follow Up Schedule</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 info-row">
                            <div class="info-label">Follow Up Required</div>
                            <div class="info-value">
                                @if($insurance->followup_required)
                                    <span class="badge bg-success">
                                        <i class="mdi mdi-check-circle me-1"></i>Yes
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="mdi mdi-close-circle me-1"></i>No
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 info-row">
                            <div class="info-label">Scheduled Date & Time</div>
                            <div class="info-value {{ ($insurance->followup_required && $insurance->followup_scheduled_at) ? '' : 'empty' }}">
                                @if($insurance->followup_required && $insurance->followup_scheduled_at)
                                    <i class="mdi mdi-calendar me-1 text-primary"></i>
                                    {{ \Carbon\Carbon::parse($insurance->followup_scheduled_at)->format('M d, Y h:i A') }}
                                @else
                                    Not scheduled
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-6">
            <!-- Live & Health Pipeline Checklist -->
            @php
                // Define the 9 stages of Live & Health process
                $pipelineStages = [
                    ['key' => 'sales', 'label' => 'Sales', 'icon' => 'mdi-cart-outline'],
                    ['key' => 'submission', 'label' => 'Submission', 'icon' => 'mdi-file-send-outline'],
                    ['key' => 'issuance', 'label' => 'Issuance/Placed Officer', 'icon' => 'mdi-shield-check-outline'],
                    ['key' => 'followup', 'label' => 'Follow up CX', 'icon' => 'mdi-phone-forward-outline'],
                    ['key' => 'banking_validation', 'label' => 'Banking Validation', 'icon' => 'mdi-bank-check'],
                    ['key' => 'draft_confirmation', 'label' => 'Confirmation of draft from bank/carrier', 'icon' => 'mdi-check-decagram'],
                    ['key' => 'commission', 'label' => 'Commission Statement', 'icon' => 'mdi-cash-multiple'],
                    ['key' => 'paid', 'label' => 'Paid', 'icon' => 'mdi-currency-usd'],
                    ['key' => 'advance_recovery', 'label' => 'Advance Recovery', 'icon' => 'mdi-cash-refund']
                ];

                // Determine which stages are completed based on lead data
                $completedStages = [];
                $currentStage = null;

                // Sales - if lead has sale_date or created
                if ($insurance->sale_date || $insurance->date || $insurance->created_at) {
                    $completedStages[] = 'sales';
                }

                // Submission - if status is accepted or further
                if (in_array($insurance->status, ['accepted', 'underwriting', 'approved'])) {
                    $completedStages[] = 'sales';
                    $completedStages[] = 'submission';
                }

                // Issuance - if issuance_status is set or policy_number exists
                if ($insurance->issuance_status === 'issued' || $insurance->policy_number || $insurance->issued_policy_number) {
                    $completedStages[] = 'sales';
                    $completedStages[] = 'submission';
                    $completedStages[] = 'issuance';
                }

                // Follow up - if followup_status is set
                if ($insurance->followup_status === 'completed' || $insurance->followup_status === 'in_progress') {
                    $completedStages[] = 'sales';
                    $completedStages[] = 'submission';
                    $completedStages[] = 'issuance';
                    if ($insurance->followup_status === 'completed') {
                        $completedStages[] = 'followup';
                    } else {
                        $currentStage = 'followup';
                    }
                }

                // Banking Validation - if bank_verification_status is approved
                if ($insurance->bank_verification_status === 'approved' || $insurance->account_verified_by) {
                    $completedStages[] = 'sales';
                    $completedStages[] = 'submission';
                    $completedStages[] = 'issuance';
                    $completedStages[] = 'followup';
                    $completedStages[] = 'banking_validation';
                }

                // Draft Confirmation - if initial_draft_date is set and passed
                if ($insurance->initial_draft_date && \Carbon\Carbon::parse($insurance->initial_draft_date)->isPast()) {
                    $completedStages[] = 'sales';
                    $completedStages[] = 'submission';
                    $completedStages[] = 'issuance';
                    $completedStages[] = 'followup';
                    $completedStages[] = 'banking_validation';
                    $completedStages[] = 'draft_confirmation';
                }

                // Commission Statement - if agent_commission is calculated
                if ($insurance->agent_commission || $insurance->commission_calculated_at) {
                    $completedStages[] = 'sales';
                    $completedStages[] = 'submission';
                    $completedStages[] = 'issuance';
                    $completedStages[] = 'followup';
                    $completedStages[] = 'banking_validation';
                    $completedStages[] = 'draft_confirmation';
                    $completedStages[] = 'commission';
                }

                // Paid - custom logic (you can add a 'payment_status' field later)
                // For now, assume if commission exists and 30+ days passed
                if ($insurance->agent_commission && $insurance->commission_calculated_at && 
                    \Carbon\Carbon::parse($insurance->commission_calculated_at)->diffInDays(now()) > 30) {
                    $completedStages[] = 'paid';
                }

                // Advance Recovery - if there's any recovery tracking (you can add field later)
                // For now, leave it unchecked unless specifically marked

                // Determine current stage if not set
                if (!$currentStage) {
                    if (!in_array('sales', $completedStages)) {
                        $currentStage = 'sales';
                    } elseif (!in_array('submission', $completedStages)) {
                        $currentStage = 'submission';
                    } elseif (!in_array('issuance', $completedStages)) {
                        $currentStage = 'issuance';
                    } elseif (!in_array('followup', $completedStages)) {
                        $currentStage = 'followup';
                    } elseif (!in_array('banking_validation', $completedStages)) {
                        $currentStage = 'banking_validation';
                    } elseif (!in_array('draft_confirmation', $completedStages)) {
                        $currentStage = 'draft_confirmation';
                    } elseif (!in_array('commission', $completedStages)) {
                        $currentStage = 'commission';
                    } elseif (!in_array('paid', $completedStages)) {
                        $currentStage = 'paid';
                    } else {
                        $currentStage = 'advance_recovery';
                    }
                }

                $completedStages = array_unique($completedStages);
            @endphp

            <div class="info-card pipeline-checklist">
                <div class="card-header-gold">
                    <h5><i class="mdi mdi-timeline-check"></i>Live & Health Pipeline</h5>
                </div>
                <div class="card-body">
                    @foreach($pipelineStages as $stage)
                        @php
                            $isCompleted = in_array($stage['key'], $completedStages);
                            $isCurrent = $stage['key'] === $currentStage;
                        @endphp
                        <div class="checklist-item {{ $isCompleted ? 'completed' : '' }} {{ $isCurrent ? 'current' : '' }}">
                            <div class="checklist-checkbox">
                                @if($isCompleted)
                                    <i class="mdi mdi-check"></i>
                                @elseif($isCurrent)
                                    <i class="mdi mdi-dots-horizontal"></i>
                                @endif
                            </div>
                            <div class="checklist-label">
                                <i class="mdi {{ $stage['icon'] }} me-1"></i>
                                {{ $stage['label'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Banking Information -->
            @hasanyrole('Super Admin|CEO|Manager|Co-ordinator')
                <div class="info-card">
                    <div class="card-header-gold">
                        <h5><i class="mdi mdi-bank"></i>Banking Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 info-row">
                                <div class="info-label">Bank Name</div>
                                <div class="info-value {{ $insurance->bank_name ? '' : 'empty' }}">
                                    {{ $insurance->bank_name ?? 'Not provided' }}
                                </div>
                            </div>
                            <div class="col-md-6 info-row">
                                <div class="info-label">Account Type</div>
                                <div class="info-value {{ $insurance->account_type ? '' : 'empty' }}">
                                    {{ $insurance->account_type ?? 'Not provided' }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 info-row">
                                <div class="info-label">Routing Number</div>
                                <div class="info-value {{ $insurance->routing_number ? '' : 'empty' }}">
                                    {{ $insurance->routing_number ?? 'Not provided' }}
                                </div>
                            </div>
                            <div class="col-md-6 info-row">
                                <div class="info-label">Account Number</div>
                                <div class="info-value {{ $insurance->acc_number ? '' : 'empty' }}">
                                    {{ $insurance->acc_number ?? 'Not provided' }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 info-row">
                                <div class="info-label">Bank Balance</div>
                                <div class="info-value {{ $insurance->bank_balance ? '' : 'empty' }}">
                                    {{ $insurance->bank_balance ? '$' . number_format($insurance->bank_balance, 2) : 'Not provided' }}
                                </div>
                            </div>
                            <div class="col-md-6 info-row">
                                <div class="info-label">Verified By</div>
                                <div class="info-value {{ $insurance->account_verified_by ? '' : 'empty' }}">
                                    {{ $insurance->account_verified_by ?? 'Not verified' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endhasanyrole

            <!-- Card Information -->
            @hasanyrole('Super Admin|CEO|Manager|Co-ordinator')
                <div class="info-card">
                    <div class="card-header-gold">
                        <h5><i class="mdi mdi-credit-card"></i>Card Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 info-row">
                                <div class="info-label">Card Number</div>
                                <div class="info-value {{ $insurance->card_number ? '' : 'empty' }}">
                                    {{ $insurance->card_number ?? 'Not provided' }}
                                </div>
                            </div>
                            <div class="col-md-6 info-row">
                                <div class="info-label">CVV</div>
                                <div class="info-value {{ $insurance->cvv ? '' : 'empty' }}">
                                    {{ $insurance->cvv ?? 'Not provided' }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 info-row">
                                <div class="info-label">Expiry Date</div>
                                <div class="info-value {{ $insurance->expiry_date ? '' : 'empty' }}">
                                    {{ $insurance->expiry_date ?? 'Not provided' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endhasanyrole

            <!-- Sales Information -->
            @hasanyrole('Super Admin|CEO|Manager|Co-ordinator')
                <div class="info-card">
                    <div class="card-header-gold">
                        <h5><i class="mdi mdi-chart-line"></i>Sales Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 info-row">
                                <div class="info-label">Source</div>
                                <div class="info-value {{ $insurance->source ? '' : 'empty' }}">
                                    {{ $insurance->source ?? 'Not provided' }}
                                </div>
                            </div>
                            <div class="col-md-6 info-row">
                                <div class="info-label">Closer Name</div>
                                <div class="info-value {{ $insurance->closer_name ? '' : 'empty' }}">
                                    {{ $insurance->closer_name ?? 'Not provided' }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 info-row">
                                <div class="info-label">Assigned Partner</div>
                                <div class="info-value {{ $insurance->assigned_partner ? '' : 'empty' }}">
                                    {{ $insurance->assigned_partner ?? 'Not assigned' }}
                                </div>
                            </div>
                            <div class="col-md-6 info-row">
                                <div class="info-label">Assigned Validator</div>
                                <div class="info-value {{ $insurance->assignedValidator ? '' : 'empty' }}">
                                    {{ $insurance->assignedValidator->name ?? 'Not assigned' }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 info-row">
                                <div class="info-label">Preset Line</div>
                                <div class="info-value">
                                    @if($insurance->preset_line)
                                        <span class="badge badge-gold">
                                            {{ $insurance->preset_line }}
                                        </span>
                                    @else
                                        <span class="empty">Not provided</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 info-row">
                                <div class="info-label">Sale Date</div>
                                <div class="info-value {{ $insurance->date ? '' : 'empty' }}">
                                    {{ $insurance->date ? \Carbon\Carbon::parse($insurance->date)->format('M d, Y') : 'Not provided' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sale Assignment / Status Management -->
                @hasanyrole('Super Admin|CEO|Manager|Co-ordinator')
                <div class="info-card">
                    <div class="card-header-gold">
                        <h5><i class="mdi mdi-check-circle"></i> Sale Assignment & Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 info-row">
                                <div class="info-label">Assigned Partner</div>
                                <div class="info-value">
                                    @if($insurance->preset_line)
                                        <span class="badge badge-gold" style="font-size: 16px; padding: 8px 16px;">
                                            {{ $insurance->preset_line }}
                                        </span>
                                    @else
                                        <span class="empty">Not assigned</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 info-row">
                                <div class="info-label">Current Status</div>
                                <div class="info-value">
                                    @if ($insurance->status == 'pending')
                                        <span class="badge bg-warning" style="font-size: 14px; padding: 6px 12px;">Pending</span>
                                    @elseif ($insurance->status == 'accepted')
                                        <span class="badge bg-success" style="font-size: 14px; padding: 6px 12px;">Approved</span>
                                    @elseif ($insurance->status == 'rejected')
                                        <span class="badge bg-danger" style="font-size: 14px; padding: 6px 12px;">Declined</span>
                                    @elseif ($insurance->status == 'underwriting')
                                        <span class="badge bg-info" style="font-size: 14px; padding: 6px 12px;">Underwriting</span>
                                    @else
                                        <span class="badge bg-secondary" style="font-size: 14px; padding: 6px 12px;">{{ ucfirst($insurance->status ?? 'Unknown') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="info-label">Update Status</div>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="button" class="btn btn-success" onclick="updateLeadStatus({{ $insurance->id }}, 'accepted')">
                                        <i class="mdi mdi-check-circle me-1"></i> Approve
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="updateLeadStatus({{ $insurance->id }}, 'rejected')">
                                        <i class="mdi mdi-close-circle me-1"></i> Decline
                                    </button>
                                    <button type="button" class="btn btn-info" onclick="updateLeadStatus({{ $insurance->id }}, 'underwriting')">
                                        <i class="mdi mdi-file-document-edit me-1"></i> Underwriting
                                    </button>
                                    <button type="button" class="btn btn-warning" onclick="updateLeadStatus({{ $insurance->id }}, 'pending')">
                                        <i class="mdi mdi-clock-outline me-1"></i> Pending
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endhasanyrole
            @endhasanyrole

            <!-- QA Information -->
            @hasanyrole('Super Admin|CEO|Manager|Co-ordinator|QA')
                <div class="info-card">
                    <div class="card-header-gold">
                        <h5><i class="mdi mdi-clipboard-check"></i>QA Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 info-row">
                                <div class="info-label">QA Status</div>
                                <div class="info-value">
                                    @if($insurance->qa_status)
                                        @php
                                            $statusClass = match(strtolower($insurance->qa_status)) {
                                                'approved' => 'bg-success',
                                                'rejected' => 'bg-danger',
                                                'in review' => 'bg-warning',
                                                'pending' => 'bg-info',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }}" style="font-size: 14px; padding: 6px 12px;">
                                            {{ $insurance->qa_status }}
                                        </span>
                                    @else
                                        <span class="empty">Not reviewed</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 info-row">
                                <div class="info-label">QA Officer</div>
                                <div class="info-value {{ $insurance->qaUser ? '' : 'empty' }}">
                                    {{ $insurance->qaUser->name ?? 'Not assigned' }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 info-row">
                                <div class="info-label">QA Reason / Notes</div>
                                <div class="info-value {{ $insurance->qa_reason ? '' : 'empty' }}">
                                    {{ $insurance->qa_reason ?? 'No QA notes' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endhasanyrole

            <!-- Notes & Comments -->
            @hasanyrole('Super Admin|CEO|Manager|Co-ordinator')
                <div class="info-card">
                    <div class="card-header-gold">
                        <h5><i class="mdi mdi-note-text"></i>Notes & Comments</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <div class="info-label">Comments</div>
                            <div class="info-value {{ $insurance->comments ? '' : 'empty' }}">
                                {{ $insurance->comments ?? 'No comments' }}
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Staff Notes</div>
                            <div class="info-value {{ $insurance->staff_notes ? '' : 'empty' }}">
                                {{ $insurance->staff_notes ?? 'No staff notes' }}
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Manager Notes</div>
                            <div class="info-value {{ $insurance->manager_notes ? '' : 'empty' }}">
                                {{ $insurance->manager_notes ?? 'No manager notes' }}
                            </div>
                        </div>
                    </div>
                </div>
            @endhasanyrole

            <!-- If not Super Admin or Manager, show comments only -->
            @hasanyrole('Agent|Employee')
                <div class="info-card">
                    <div class="card-header-gold">
                        <h5><i class="mdi mdi-comment-text"></i>Comments</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <div class="info-label">Comments</div>
                            <div class="info-value {{ $insurance->comments ? '' : 'empty' }}">
                                {{ $insurance->comments ?? 'No comments' }}
                            </div>
                        </div>
                    </div>
                </div>
            @endhasanyrole
        </div>
    </div>
@endsection

@section('script')
    <script>
        // Zoom Call Function
        function makeZoomCall() {
            const phoneNumber = '{{ $insurance->phone_number ?? '' }}';
            const sanitizedZoomNumber = '{{ Auth::user()->sanitized_zoom_number ?? '' }}';

            if (!phoneNumber) {
                alert('No phone number available for this lead.');
                return;
            }

            if (!sanitizedZoomNumber) {
                alert('You do not have a Zoom phone number configured.');
                return;
            }

            // Clean the phone number (remove spaces, dashes, parentheses)
            const cleanNumber = phoneNumber.replace(/[\s\-\(\)]/g, '');

            // Create the Zoom phone URL
            const zoomUrl = `zoomphonenumber://call?to=${cleanNumber}`;

            // Try to open Zoom
            window.location.href = zoomUrl;

            // Optional: Log the call attempt
            console.log('Attempting to call:', cleanNumber);
        }

        // Update Lead Status Function
        function updateLeadStatus(leadId, status) {
            const statusLabels = {
                'accepted': 'Approved',
                'rejected': 'Declined',
                'underwriting': 'Underwriting',
                'pending': 'Pending'
            };

            if (confirm(`Are you sure you want to mark this lead as ${statusLabels[status]}?`)) {
                fetch(`/leads/${leadId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ status: status })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error updating status: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating the status');
                });
            }
        }
    </script>
@endsection
