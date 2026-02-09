@extends('layouts.master')

@section('title')
    Cluster Overview
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Analytics
        @endslot
        @slot('title')
            Cluster Overview
        @endslot
    @endcomponent

    <div class="row mb-3">
        <div class="col-12">
            <h2 class="text-primary fw-bold">
                <i class="mdi mdi-chart-box-outline me-2"></i>Cluster Overview
            </h2>
            <p class="text-muted">View all carriers, their partner assignments, and performance metrics</p>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Carriers</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="{{ $totalCarriers }}">{{ $totalCarriers }}</span>
                            </h4>
                        </div>
                        <div class="flex-shrink-0 text-end dash-widget">
                            <div class="avatar-sm rounded-circle bg-primary-subtle mx-auto">
                                <span class="avatar-title bg-primary-subtle text-primary fs-3">
                                    <i class="mdi mdi-briefcase"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Active Partners</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="{{ $totalPartners }}">{{ $totalPartners }}</span>
                            </h4>
                        </div>
                        <div class="flex-shrink-0 text-end dash-widget">
                            <div class="avatar-sm rounded-circle bg-success-subtle mx-auto">
                                <span class="avatar-title bg-success-subtle text-success fs-3">
                                    <i class="mdi mdi-account-group"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">States Covered</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="{{ $totalStates }}">{{ $totalStates }}</span>
                            </h4>
                        </div>
                        <div class="flex-shrink-0 text-end dash-widget">
                            <div class="avatar-sm rounded-circle bg-info-subtle mx-auto">
                                <span class="avatar-title bg-info-subtle text-info fs-3">
                                    <i class="mdi mdi-map-marker-multiple"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Leads</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="{{ $totalLeads }}">{{ $totalLeads }}</span>
                            </h4>
                        </div>
                        <div class="flex-shrink-0 text-end dash-widget">
                            <div class="avatar-sm rounded-circle bg-warning-subtle mx-auto">
                                <span class="avatar-title bg-warning-subtle text-warning fs-3">
                                    <i class="mdi mdi-file-document-multiple"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Carrier Cards Grid --}}
    <div class="row">
        @forelse($partnerCarriers as $pc)
        <div class="col-xl-4 col-lg-6">
            <div class="card border">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-briefcase-outline me-2"></i>{{ $pc['carrier']->name }}
                        </h5>
                        @if($pc['carrier']->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                    <div class="mt-2">
                        <span class="badge bg-primary">
                            <i class="mdi mdi-account me-1"></i>Partner: {{ $pc['partner']->name }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Carrier Details --}}
                    <div class="mb-3">
                        <div class="row g-2">
                            <div class="col-6">
                                <small class="text-muted">Payment Module</small>
                                <p class="mb-0 fw-semibold">{{ ucwords(str_replace('_', ' ', $pc['carrier']->payment_module)) }}</p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Base Commission</small>
                                <p class="mb-0 fw-semibold text-primary">{{ $pc['carrier']->base_commission_percentage ?? 0 }}%</p>
                            </div>
                        </div>
                    </div>

                    {{-- Statistics --}}
                    <div class="row g-3 mb-3">
                        <div class="col-6 text-center">
                            <div class="p-2 bg-info-subtle rounded">
                                <h4 class="mb-0 text-info">{{ $pc['state_count'] }}</h4>
                                <small class="text-muted">States</small>
                            </div>
                        </div>
                        <div class="col-6 text-center">
                            <div class="p-2 bg-success-subtle rounded">
                                <h4 class="mb-0 text-success">{{ $pc['leads_count'] }}</h4>
                                <small class="text-muted">Leads</small>
                            </div>
                        </div>
                    </div>

                    {{-- Settlement Percentages --}}
                    <div class="mb-3">
                        <small class="text-muted d-block mb-2">Average Settlement Rates</small>
                        <div class="row g-2">
                            @if($pc['avg_level'])
                            <div class="col-6">
                                <div class="bg-primary-subtle p-2 rounded text-center">
                                    <small class="text-muted d-block">Level</small>
                                    <strong class="text-primary">{{ number_format($pc['avg_level'], 2) }}%</strong>
                                </div>
                            </div>
                            @endif
                            @if($pc['avg_graded'])
                            <div class="col-6">
                                <div class="bg-info-subtle p-2 rounded text-center">
                                    <small class="text-muted d-block">Graded</small>
                                    <strong class="text-info">{{ number_format($pc['avg_graded'], 2) }}%</strong>
                                </div>
                            </div>
                            @endif
                            @if($pc['avg_gi'])
                            <div class="col-6">
                                <div class="bg-warning-subtle p-2 rounded text-center">
                                    <small class="text-muted d-block">GI</small>
                                    <strong class="text-warning">{{ number_format($pc['avg_gi'], 2) }}%</strong>
                                </div>
                            </div>
                            @endif
                            @if($pc['avg_modified'])
                            <div class="col-6">
                                <div class="bg-secondary-subtle p-2 rounded text-center">
                                    <small class="text-muted d-block">Modified</small>
                                    <strong class="text-secondary">{{ number_format($pc['avg_modified'], 2) }}%</strong>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Licensed States --}}
                    <div class="mb-3">
                        <small class="text-muted d-block mb-2">Licensed States ({{ $pc['state_count'] }})</small>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($pc['states'] as $state)
                                <span class="badge bg-secondary-subtle text-secondary">{{ $state }}</span>
                            @endforeach
                        </div>
                    </div>

                    {{-- Plan Types --}}
                    @if($pc['carrier']->plan_types && is_array($pc['carrier']->plan_types) && count($pc['carrier']->plan_types) > 0)
                    <div class="mb-3">
                        <small class="text-muted d-block mb-2">Plan Types</small>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($pc['carrier']->plan_types as $plan)
                                <span class="badge bg-secondary-subtle text-secondary">{{ $plan }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Contact Info --}}
                    @if($pc['carrier']->phone)
                    <div class="mt-3 pt-3 border-top">
                        <small class="text-muted"><i class="mdi mdi-phone me-1"></i>{{ $pc['carrier']->phone }}</small>
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">Partner & Carrier Analytics</small>
                        <div class="d-flex gap-2">
                            @if(isset($pc['partner']->is_partner_model) && $pc['partner']->is_partner_model)
                                <a href="{{ route('admin.partners.edit', $pc['partner']->id) }}" class="btn btn-sm btn-outline-primary" title="Edit Partner">
                                    <i class="mdi mdi-account-edit me-1"></i>Edit Partner
                                </a>
                            @else
                                <a href="{{ route('agents.edit', $pc['partner']->id) }}" class="btn btn-sm btn-outline-primary" title="Edit Agent">
                                    <i class="mdi mdi-account-edit me-1"></i>Edit Agent
                                </a>
                            @endif
                            <a href="{{ route('admin.insurance-carriers.edit', $pc['carrier']->id) }}" class="btn btn-sm btn-outline-info" title="Edit Carrier">
                                <i class="mdi mdi-briefcase-edit me-1"></i>Edit Carrier
                            </a>
                            <form action="{{ route('admin.insurance-carriers.destroy', $pc['carrier']->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('⚠️ DANGER: Are you sure you want to PERMANENTLY DELETE the carrier {{ $pc['carrier']->name }} from the entire system? This will remove ALL partner assignments and cannot be undone!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete Carrier Permanently">
                                    <i class="mdi mdi-delete-forever me-1"></i>Delete Carrier
                                </button>
                            </form>
                            @if(isset($pc['partner']->is_partner_model) && $pc['partner']->is_partner_model)
                                <form action="{{ route('admin.partners.remove-carrier-assignment', [$pc['partner']->id, $pc['carrier']->id]) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to remove {{ $pc['carrier']->name }} assignment from partner {{ $pc['partner']->name }}? This will remove all state assignments for this carrier.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove Carrier Assignment">
                                        <i class="mdi mdi-delete me-1"></i>Remove Assignment
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.partners.remove-carrier-assignment', [$pc['partner']->id, $pc['carrier']->id]) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to remove {{ $pc['carrier']->name }} assignment from agent {{ $pc['partner']->name }}? This will remove all state assignments for this carrier.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove Carrier Assignment">
                                        <i class="mdi mdi-delete me-1"></i>Remove Assignment
                                    </button>
                                </form>
                            @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="mdi mdi-briefcase-outline display-4 text-muted mb-3"></i>
                    <h5 class="text-muted">No Carrier Assignments Found</h5>
                    <p class="text-muted">Add carriers through the Partner management page</p>
                    <a href="{{ route('agents.index') }}" class="btn btn-primary">
                        <i class="mdi mdi-account-plus me-1"></i>Go to Partners
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

@endsection