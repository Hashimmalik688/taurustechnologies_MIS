@extends('layouts.master')

@section('title')
    Partners Management
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Admin
        @endslot
        @slot('title')
            Partners
        @endslot
    @endcomponent

    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="text-primary fw-bold mb-0">
                        <i class="mdi mdi-account-group me-2"></i>Partners Management
                    </h2>
                    <p class="text-muted">Manage external partners and their carrier assignments</p>
                </div>
                <a href="{{ route('admin.partners.create') }}" class="btn btn-primary">
                    <i class="mdi mdi-plus me-1"></i>Add New Partner
                </a>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Partner Code</th>
                                    <th>Partner Name</th>
                                    <th>Email</th>
                                    <th>Carriers</th>
                                    <th>States</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($partners as $partner)
                                    @php
                                        $uniqueCarriers = $partner->carrierStates->pluck('insurance_carrier_id')->unique();
                                        $totalStates = $partner->carrierStates->pluck('state')->unique()->count();
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary">{{ $partner->code }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                        {{ substr($partner->name, 0, 2) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $partner->name }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($partner->email)
                                                <a href="mailto:{{ $partner->email }}" class="text-muted">
                                                    <i class="mdi mdi-email me-1"></i>{{ $partner->email }}
                                                </a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info-subtle text-info">
                                                {{ $uniqueCarriers->count() }} Carriers
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success-subtle text-success">
                                                {{ $totalStates }} States
                                            </span>
                                        </td>
                                        <td>
                                            @if($partner->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('admin.partners.show', $partner->id) }}" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   title="View Details">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.partners.edit', $partner->id) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Edit Partner">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                <form action="{{ route('admin.partners.destroy', $partner->id) }}" 
                                                      method="POST" 
                                                      style="display: inline;" 
                                                      onsubmit="return confirm('Are you sure you want to delete {{ $partner->name }}? This will remove all carrier assignments.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            title="Delete Partner">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="mdi mdi-account-off mdi-48px mb-3 d-block"></i>
                                                <h5>No Partners Found</h5>
                                                <p>Click "Add New Partner" to create your first partner</p>
                                            </div>
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
@endsection

@section('css')
    <style>
        .avatar-sm {
            height: 2.5rem;
            width: 2.5rem;
        }

        .avatar-title {
            align-items: center;
            background-color: #556ee6;
            color: #fff;
            display: flex;
            font-weight: 500;
            height: 100%;
            justify-content: center;
            width: 100%;
        }

        .bg-soft-primary {
            background-color: rgba(85, 110, 230, 0.1) !important;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
    </style>
@endsection
