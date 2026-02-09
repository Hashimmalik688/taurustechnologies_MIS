@extends('layouts.master')

@section('title', 'Account Switching Log')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Account Switching Log</h1>
    <p class="text-muted">Device fingerprints used by multiple users. This may indicate account sharing or suspicious logins.</p>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Device Fingerprint</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>IP Address</th>
                    <th>Login Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->device_fingerprint }}</td>
                        <td>{{ $log->user ? $log->user->name : 'Unknown' }}</td>
                        <td>{{ $log->user ? $log->user->email : $log->user_email }}</td>
                        <td>{{ $log->ip_address }}</td>
                        <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No suspicious account switching detected.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
