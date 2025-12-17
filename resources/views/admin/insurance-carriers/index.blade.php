@extends('layouts.master')

@section('title')
    Insurance Carriers
@endsection

@section('css')
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Partners
        @endslot
        @slot('title')
            Insurance Carriers
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
                <div class="d-flex justify-content-between align-items-center p-3">
                    <h4 class="card-title mb-0">Insurance Carriers Management</h4>
                    <a class="btn btn-success btn-sm waves-effect waves-light" href="{{ route('admin.insurance-carriers.create') }}">
                        <i class="bx bx-plus font-size-16 align-middle me-1"></i> Add Carrier
                    </a>
                </div>

                <div class="card-body">
                    <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Commission Structure</th>
                                <th>Plan Types</th>
                                <th>Status</th>
                                <th>Leads Count</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($carriers as $carrier)
                                <tr>
                                    <td>{{ $carrier->id }}</td>
                                    <td><strong>{{ $carrier->name }}</strong></td>
                                    <td>
                                        @if($carrier->commissionBrackets && $carrier->commissionBrackets->count() > 0)
                                            <div class="d-flex flex-column gap-1">
                                                @foreach($carrier->commissionBrackets as $bracket)
                                                    <div>
                                                        <span class="badge bg-primary">
                                                            Ages {{ $bracket->age_min }}-{{ $bracket->age_max }}: {{ number_format($bracket->commission_percentage, 2) }}%
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <small class="text-muted d-block mt-1">Formula: Premium × 9 months × %</small>
                                        @elseif($carrier->base_commission_percentage)
                                            <span class="badge bg-secondary">
                                                {{ number_format($carrier->base_commission_percentage, 2) }}% (All Ages)
                                            </span>
                                            <br><small class="text-muted">Formula: Premium × 9 months × {{ number_format($carrier->base_commission_percentage, 2) }}%</small>
                                        @else
                                            <span class="text-muted">Not configured</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($carrier->plan_types && count($carrier->plan_types) > 0)
                                            @foreach($carrier->plan_types as $plan)
                                                <span class="badge bg-info me-1">{{ $plan }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($carrier->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $carrier->leads()->count() }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.insurance-carriers.edit', $carrier->id) }}" 
                                               class="btn btn-sm btn-primary" title="Edit">
                                                <i class="bx bx-edit font-size-16"></i>
                                            </a>
                                            <form action="{{ route('admin.insurance-carriers.destroy', $carrier->id) }}" 
                                                  method="POST" 
                                                  style="display:inline;" 
                                                  onsubmit="return confirm('Are you sure you want to delete this carrier?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="bx bx-trash font-size-16"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#datatable').DataTable({
                order: [[1, 'asc']], // Sort by name
                pageLength: 25
            });
        });
    </script>
@endsection
