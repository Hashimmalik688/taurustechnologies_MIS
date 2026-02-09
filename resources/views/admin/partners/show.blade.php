@extends('layouts.master')

@section('title')
    Partner Details
@endsection

@section('css')
    <style>
        /* ===== Animated Background ===== */
        .partners-animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
            background: linear-gradient(135deg, #667eea10 0%, #764ba210 100%);
        }

        .gradient-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.5;
            animation: float 15s infinite ease-in-out;
        }

        .orb-1 {
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            top: -200px;
            right: -200px;
        }

        .orb-2 {
            width: 350px;
            height: 350px;
            background: linear-gradient(135deg, #2193b0, #6dd5ed);
            bottom: -175px;
            left: -175px;
            animation-delay: 7s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(30px, -30px); }
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .partner-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 16px 16px 0 0;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        .partner-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.08'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .partner-avatar {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: 700;
            text-transform: uppercase;
            position: relative;
            z-index: 1;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 0.5rem;
            animation: pulse 2s infinite;
        }

        .status-active .status-dot {
            background: #38ef7d;
            box-shadow: 0 0 0 0 rgba(56, 239, 125, 0.7);
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(56, 239, 125, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(56, 239, 125, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(56, 239, 125, 0);
            }
        }

        .info-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .info-card:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.1);
        }

        .info-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .info-value {
            font-size: 1.125rem;
            color: #1a1a1a;
            font-weight: 600;
        }

        .carrier-card {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .carrier-card:hover {
            border-color: #667eea;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.15);
        }

        .carrier-card h5 {
            color: #667eea;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .state-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            margin: 0.25rem;
        }

        .settlement-row {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 0.75rem;
            border: 1px solid #dee2e6;
        }

        .settlement-row strong {
            color: #667eea;
        }

        .btn-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 0.625rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        }

        .btn-gradient-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .btn-secondary {
            border-radius: 12px;
            padding: 0.625rem 1.5rem;
            font-weight: 600;
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            <a href="{{ route('admin.partners.index') }}">Partners</a>
        @endslot
        @slot('title')
            Partner Details
        @endslot
    @endcomponent

    <!-- Animated Background -->
    <div class="partners-animated-bg">
        <div class="gradient-orb orb-1"></div>
        <div class="gradient-orb orb-2"></div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card glass-card">
                <!-- Partner Header -->
                <div class="partner-header">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="d-flex align-items-center">
                                <div class="partner-avatar me-4">
                                    {{ substr($partner->name, 0, 2) }}
                                </div>
                                <div>
                                    <h2 class="mb-1">{{ $partner->name }}</h2>
                                    <p class="mb-0 opacity-75">
                                        <i class="mdi mdi-key-variant me-1"></i>
                                        Partner Code: <strong>{{ $partner->code }}</strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                            @if($partner->is_active)
                                <span class="status-badge status-active">
                                    <span class="status-dot"></span>Active Partner
                                </span>
                            @else
                                <span class="status-badge">
                                    <span class="status-dot" style="background: #6c757d;"></span>Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Partner Details -->
                <div class="card-body p-4">
                    <h5 class="mb-4">
                        <i class="mdi mdi-information-outline me-2"></i>
                        Contact Information
                    </h5>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-card">
                                <div class="info-label">
                                    <i class="mdi mdi-email-outline me-1"></i>Email
                                </div>
                                <div class="info-value">
                                    @if($partner->email)
                                        <a href="mailto:{{ $partner->email }}" class="text-decoration-none">
                                            {{ $partner->email }}
                                        </a>
                                    @else
                                        <span class="text-muted">Not provided</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="info-card">
                                <div class="info-label">
                                    <i class="mdi mdi-phone-outline me-1"></i>Phone
                                </div>
                                <div class="info-value">
                                    @if($partner->phone)
                                        {{ $partner->phone }}
                                    @else
                                        <span class="text-muted">Not provided</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="info-card">
                                <div class="info-label">
                                    <i class="mdi mdi-shield-lock-outline me-1"></i>SSN (Last 4)
                                </div>
                                <div class="info-value">
                                    @if($partner->ssn_last4)
                                        •••• {{ $partner->ssn_last4 }}
                                    @else
                                        <span class="text-muted">Not provided</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Carrier & State Assignments -->
                    <h5 class="mb-4">
                        <i class="mdi mdi-briefcase-outline me-2"></i>
                        Carrier & State Assignments
                    </h5>

                    @php
                        $groupedCarrierStates = $partner->carrierStates->groupBy('insurance_carrier_id');
                    @endphp

                    @forelse($groupedCarrierStates as $carrierId => $carrierStates)
                        @php
                            $carrier = $carrierStates->first()->insuranceCarrier;
                        @endphp
                        <div class="carrier-card">
                            <h5>
                                <i class="mdi mdi-shield-check me-2"></i>
                                {{ $carrier->name ?? 'Unknown Carrier' }}
                            </h5>

                            <div class="mb-3">
                                <strong class="d-block mb-2">Licensed States ({{ $carrierStates->count() }}):</strong>
                                @foreach($carrierStates as $cs)
                                    <span class="state-badge">{{ $cs->state }}</span>
                                @endforeach
                            </div>

                            <hr>

                            <strong class="d-block mb-2">Settlement Percentages:</strong>
                            @foreach($carrierStates as $cs)
                                <div class="settlement-row">
                                    <strong>{{ $cs->state }}:</strong>
                                    @if($cs->settlement_level_pct)
                                        Level: {{ number_format($cs->settlement_level_pct, 2) }}%
                                    @endif
                                    @if($cs->settlement_graded_pct)
                                        | Graded: {{ number_format($cs->settlement_graded_pct, 2) }}%
                                    @endif
                                    @if($cs->settlement_gi_pct)
                                        | GI: {{ number_format($cs->settlement_gi_pct, 2) }}%
                                    @endif
                                    @if($cs->settlement_modified_pct)
                                        | Modified: {{ number_format($cs->settlement_modified_pct, 2) }}%
                                    @endif
                                    @if(!$cs->settlement_level_pct && !$cs->settlement_graded_pct && !$cs->settlement_gi_pct && !$cs->settlement_modified_pct)
                                        <span class="text-muted">No percentages set</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @empty
                        <div class="alert alert-info">
                            <i class="mdi mdi-information me-2"></i>
                            No carrier or state assignments for this partner yet.
                        </div>
                    @endforelse

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.partners.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left me-1"></i>Back to List
                        </a>
                        <a href="{{ route('admin.partners.edit', $partner->id) }}" class="btn btn-gradient-primary">
                            <i class="mdi mdi-pencil me-1"></i>Edit Partner
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
