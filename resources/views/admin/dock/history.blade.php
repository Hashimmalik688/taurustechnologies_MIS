@extends('layouts.master')

@section('title', 'Dock History - ' . $user->name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <a href="{{ route('dock.index') }}" class="btn btn-secondary">
                    <i class="mdi mdi-arrow-left"></i> Back to Dock Section
                </a>
            </div>
            <h4 class="page-title">Dock History - {{ $user->name }}</h4>
        </div>
    </div>
</div>

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

<!-- Dock History Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Complete Dock History</h4>

                @if ($dockRecords->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-centered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Amount</th>
                                <th>Reason</th>
                                <th>Dock Date</th>
                                <th>Month/Year</th>
                                <th>Docked By</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dockRecords as $record)
                            <tr>
                                <td>{{ $record->id }}</td>
                                <td><strong>Rs {{ number_format($record->amount, 2) }}</strong></td>
                                <td>{{ Str::limit($record->reason, 50) }}</td>
                                <td>{{ $record->dock_date->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::create($record->dock_year, $record->dock_month)->format('M Y') }}</td>
                                <td>{{ $record->dockedBy->name }}</td>
                                <td>
                                    @if ($record->status === 'active')
                                        <span class="badge bg-warning">Active</span>
                                    @elseif ($record->status === 'applied')
                                        <span class="badge bg-success">Applied</span>
                                    @else
                                        <span class="badge bg-secondary">Cancelled</span>
                                    @endif
                                </td>
                                <td>{{ $record->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            @if ($record->notes)
                            <tr>
                                <td colspan="8" class="text-muted small ps-5">
                                    <i class="mdi mdi-note-text"></i> {{ $record->notes }}
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="1"><strong>Summary:</strong></td>
                                <td colspan="7">
                                    <strong>Active Docks: Rs {{ number_format($totalDocked, 2) }}</strong>
                                    | Total Records: {{ $dockRecords->total() }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $dockRecords->links() }}
                </div>
                @else
                <div class="text-center py-4">
                    <i class="mdi mdi-information-outline" style="font-size: 48px; color: #ccc;"></i>
                    <p class="text-muted mt-2">No dock records found for this employee.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
