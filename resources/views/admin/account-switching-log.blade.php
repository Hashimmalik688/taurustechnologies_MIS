@extends('layouts.master')

@section('title', 'Account Switch Log')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-3">Account Switch Log</h1>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('admin.account-switching-log') }}">
                <i class="bx bx-transfer me-1"></i> Suspicious Devices
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.audit-logs.index') }}">
                <i class="bx bx-list-ul me-1"></i> All Activity Logs
            </a>
        </li>
    </ul>

    <p class="text-muted">
        Devices used by multiple users to log in. This indicates account sharing or suspicious activity on the same PC.
    </p>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Device</th>
                    <th>Fingerprint</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>IP Address</th>
                    <th>Login Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->device_name ?: 'Unknown Device' }}</td>
                        <td><code>{{ $log->device_fingerprint }}</code></td>
                        <td>{{ $log->user ? $log->user->name : 'Unknown' }}</td>
                        <td>{{ $log->user ? $log->user->email : $log->user_email }}</td>
                        <td>{{ $log->ip_address }}</td>
                        <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No suspicious account switching detected.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
