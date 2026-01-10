@extends('layouts.master')

@section('title')
    My Dock Records
@endsection

@section('css')
    <link href="{{ URL::asset('public/css/light-theme.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Employee
        @endslot
        @slot('title')
            My Dock Records
        @endslot
    @endcomponent

    <!-- Summary Card -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="text-white mb-1">{{ $user->name }}</h5>
                            <p class="mb-0">
                                <span class="badge bg-light text-dark">
                                    @foreach($user->roles as $role)
                                        {{ $role->name }}{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </span>
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <h2 class="text-white mb-0">Rs {{ number_format($totalDocked, 2) }}</h2>
                            <small>Total Active Docks</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dock Records Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">My Dock Records</h4>
                    <p class="text-muted">View all your dock records including reasons and who applied them.</p>

                    @if ($dockRecords->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-centered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Reason</th>
                                    <th>Applied By</th>
                                    <th>Status</th>
                                    <th>Month Applied</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dockRecords as $record)
                                <tr>
                                    <td>{{ $record->dock_date->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge bg-danger fs-6">Rs {{ number_format($record->amount, 2) }}</span>
                                    </td>
                                    <td>
                                        <div class="reason-text">
                                            {{ $record->reason }}
                                        </div>
                                        @if ($record->notes)
                                            <small class="text-muted d-block mt-1">
                                                <i class="mdi mdi-note-text"></i> {{ $record->notes }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs me-2">
                                                <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                    {{ substr($record->dockedBy->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $record->dockedBy->name }}</h6>
                                                <small class="text-muted">{{ $record->created_at->format('g:i A') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($record->status === 'active')
                                            <span class="badge bg-warning">Active</span>
                                        @elseif ($record->status === 'applied')
                                            <span class="badge bg-success">Applied to Salary</span>
                                        @else
                                            <span class="badge bg-secondary">Cancelled</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::create($record->dock_year, $record->dock_month)->format('M Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary Statistics -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card bg-soft-warning">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Total Active Docks</h5>
                                    <h3 class="text-warning">Rs {{ number_format($totalDocked, 2) }}</h3>
                                    <small class="text-muted">This amount will be deducted from your salary</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-soft-info">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Total Records</h5>
                                    <h3 class="text-info">{{ $dockRecords->total() }}</h3>
                                    <small class="text-muted">All dock records (active, applied, cancelled)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        {{ $dockRecords->links() }}
                    </div>
                    @else
                    <div class="text-center py-5">
                        <div class="avatar-lg mx-auto mb-3">
                            <div class="avatar-title bg-soft-success text-success rounded-circle">
                                <i class="bx bx-check-circle" style="font-size: 32px;"></i>
                            </div>
                        </div>
                        <h5 class="text-success">Great Job!</h5>
                        <p class="text-muted">You have no dock records. Keep up the excellent work!</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Information Panel -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-soft-info">
                <div class="card-body">
                    <h6 class="card-title text-info">
                        <i class="mdi mdi-information-outline me-1"></i>
                        Understanding Dock Records
                    </h6>
                    <div class="row">
                        <div class="col-md-4">
                            <h6 class="text-dark">Status Meanings:</h6>
                            <ul class="list-unstyled mb-0">
                                <li><span class="badge bg-warning me-2">Active</span>Pending deduction from salary</li>
                                <li><span class="badge bg-success me-2">Applied</span>Already deducted from salary</li>
                                <li><span class="badge bg-secondary me-2">Cancelled</span>Dock was cancelled/removed</li>
                            </ul>
                        </div>
                        <div class="col-md-8">
                            <h6 class="text-dark">Important Information:</h6>
                            <ul class="list-unstyled mb-0">
                                <li><i class="mdi mdi-check-circle text-success me-2"></i>Active docks will be deducted from your next salary</li>
                                <li><i class="mdi mdi-check-circle text-success me-2"></i>Applied docks have already been deducted and won't be charged again</li>
                                <li><i class="mdi mdi-check-circle text-success me-2"></i>If you have questions about any dock, contact your supervisor or HR</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
<script>
    // Add any JavaScript if needed
</script>
@endsection