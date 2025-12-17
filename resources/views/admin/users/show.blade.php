@extends('layouts.master')

@section('title')
    View User
@endsection

@section('css')
<style>
    .info-card {
        border-radius: 8px;
        box-shadow: 0px 0px 14px 4px #12263f24;
        transition: all 0.3s ease;
    }

    .info-card:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .info-label {
        font-size: 12px;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .info-value {
        font-size: 15px;
        font-weight: 500;
        color: #495057;
    }

    .section-title {
        font-size: 17px;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
        color: #556ee6;
    }

    .user-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        color: white;
        font-weight: bold;
        margin: 0 auto 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .stat-box {
        text-align: center;
        padding: 20px;
        border-radius: 8px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        margin-bottom: 15px;
    }

    .stat-number {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 13px;
        opacity: 0.9;
        text-transform: uppercase;
    }

    .badge-role {
        padding: 8px 15px;
        font-size: 13px;
        font-weight: 600;
        border-radius: 20px;
    }

    .badge-manager {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }

    .badge-employee {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }

    .badge-active {
        background-color: #34c38f;
        color: white;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
    }

    .badge-inactive {
        background-color: #f46a6a;
        color: white;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
    }
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Users
        @endslot
        @slot('title')
            View User Details
        @endslot
    @endcomponent

    <div class="row">
        <!-- Left Column - User Info -->
        <div class="col-lg-4">
            <div class="card info-card">
                <div class="card-body">
                    <div class="user-avatar">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>

                    <div class="text-center mb-4">
                        <h4 class="mb-1">{{ $user->name }}</h4>
                        <p class="text-muted mb-2">{{ $user->email }}</p>
                        @if($user->userDetail)
                            <span class="badge badge-role {{ $user->userDetail->role == 'Manager' ? 'badge-manager' : 'badge-employee' }}">
                                <i class="mdi mdi-account-circle me-1"></i>
                                {{ $user->userDetail->role ?? 'N/A' }}
                            </span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            @if($user->deleted_at)
                                <span class="badge-inactive">
                                    <i class="mdi mdi-close-circle me-1"></i>Inactive
                                </span>
                            @else
                                <span class="badge-active">
                                    <i class="mdi mdi-check-circle me-1"></i>Active
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($user->userDetail)
                        <div class="mb-3">
                            <div class="info-label">Phone</div>
                            <div class="info-value">
                                <i class="mdi mdi-phone me-1 text-primary"></i>
                                {{ $user->userDetail->phone ?? 'Not provided' }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="info-label">Zoom Number</div>
                            <div class="info-value">
                                <i class="mdi mdi-video me-1 text-primary"></i>
                                {{ $user->userDetail->zoom_number ?? 'Not provided' }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="info-label">Gender</div>
                            <div class="info-value">
                                <i class="mdi mdi-gender-{{ strtolower($user->userDetail->gender ?? 'male') }} me-1 text-primary"></i>
                                {{ $user->userDetail->gender ?? 'Not specified' }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="info-label">Date of Birth</div>
                            <div class="info-value">
                                <i class="mdi mdi-cake-variant me-1 text-primary"></i>
                                {{ $user->dob ? $user->dob->format('M d, Y') : 'Not provided' }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="info-label">Join Date</div>
                            <div class="info-value">
                                <i class="mdi mdi-calendar me-1 text-primary"></i>
                                {{ $user->userDetail->join_date ?? 'Not provided' }}
                            </div>
                        </div>

                        @if($user->userDetail->city || $user->userDetail->address)
                            <div class="mb-3">
                                <div class="info-label">Location</div>
                                <div class="info-value">
                                    <i class="mdi mdi-map-marker me-1 text-primary"></i>
                                    @if($user->userDetail->city)
                                        {{ $user->userDetail->city }}
                                    @endif
                                    @if($user->userDetail->address)
                                        <br><small class="text-muted">{{ $user->userDetail->address }}</small>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endif

                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary">
                            <i class="mdi mdi-pencil me-1"></i>
                            Edit User
                        </a>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                            <i class="mdi mdi-arrow-left me-1"></i>
                            Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Statistics & Activity -->
        <div class="col-lg-8">
            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-md-4">
                    <div class="stat-box">
                        <div class="stat-number">
                            {{ $user->attendances()->count() }}
                        </div>
                        <div class="stat-label">
                            <i class="mdi mdi-calendar-check me-1"></i>
                            Total Attendance
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                        <div class="stat-number">
                            {{ $user->attendances()->whereMonth('created_at', now()->month)->count() }}
                        </div>
                        <div class="stat-label">
                            <i class="mdi mdi-calendar-month me-1"></i>
                            This Month
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
                        <div class="stat-number">
                            @if($user->todayAttendance)
                                {{ $user->todayAttendance->check_in ? $user->todayAttendance->check_in->format('h:i A') : 'N/A' }}
                            @else
                                --
                            @endif
                        </div>
                        <div class="stat-label">
                            <i class="mdi mdi-clock-check me-1"></i>
                            Today Check-in
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card info-card">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="mdi mdi-history me-2"></i>
                        Recent Attendance
                    </h5>

                    @if($user->attendances()->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Check In</th>
                                        <th>Check Out</th>
                                        <th>Duration</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->attendances()->latest()->take(10)->get() as $attendance)
                                        <tr>
                                            <td>
                                                <i class="mdi mdi-calendar me-1 text-primary"></i>
                                                {{ $attendance->created_at->format('M d, Y') }}
                                            </td>
                                            <td>
                                                <i class="mdi mdi-clock-in me-1 text-success"></i>
                                                {{ $attendance->check_in ? $attendance->check_in->format('h:i A') : 'N/A' }}
                                            </td>
                                            <td>
                                                <i class="mdi mdi-clock-out me-1 text-danger"></i>
                                                {{ $attendance->check_out ? $attendance->check_out->format('h:i A') : 'N/A' }}
                                            </td>
                                            <td>
                                                @if($attendance->check_in && $attendance->check_out)
                                                    {{ $attendance->check_in->diffForHumans($attendance->check_out, true) }}
                                                @else
                                                    <span class="text-muted">Ongoing</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($attendance->check_out)
                                                    <span class="badge bg-success">Completed</span>
                                                @else
                                                    <span class="badge bg-warning">In Progress</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="mdi mdi-calendar-remove display-4 text-muted"></i>
                            <p class="text-muted mt-3">No attendance records found</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Account Information -->
            <div class="card info-card">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="mdi mdi-information me-2"></i>
                        Account Information
                    </h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="info-label">Account Created</div>
                            <div class="info-value">
                                <i class="mdi mdi-calendar-plus me-1 text-primary"></i>
                                {{ $user->created_at->format('M d, Y h:i A') }}
                                <br>
                                <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-label">Last Updated</div>
                            <div class="info-value">
                                <i class="mdi mdi-calendar-edit me-1 text-primary"></i>
                                {{ $user->updated_at->format('M d, Y h:i A') }}
                                <br>
                                <small class="text-muted">{{ $user->updated_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-label">User ID</div>
                            <div class="info-value">
                                <i class="mdi mdi-identifier me-1 text-primary"></i>
                                #{{ str_pad($user->id, 6, '0', STR_PAD_LEFT) }}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="info-label">Email Verified</div>
                            <div class="info-value">
                                @if($user->email_verified_at)
                                    <i class="mdi mdi-check-circle me-1 text-success"></i>
                                    <span class="text-success">Verified</span>
                                @else
                                    <i class="mdi mdi-close-circle me-1 text-danger"></i>
                                    <span class="text-danger">Not Verified</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
@endsection
