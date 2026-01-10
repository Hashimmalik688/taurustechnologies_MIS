@extends('layouts.master')

@section('title')
    My Dashboard
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
            My Dashboard
        @endslot
    @endcomponent

    <div class="row">
        <!-- Dialed -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100 bordered">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Dialed</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="{{ $stats['dialed_today'] ?? 0 }}">{{ $stats['dialed_today'] ?? 0 }}</span>
                            </h4>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <div class="avatar-sm rounded-circle bg-soft-primary align-self-center">
                                <span class="avatar-title bg-primary rounded-circle fs-3">
                                    <i class="bx bx-phone text-white"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calls Connected -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100 bordered">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Calls Connected</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="{{ $stats['calls_connected'] ?? 0 }}">{{ $stats['calls_connected'] ?? 0 }}</span>
                            </h4>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <div class="avatar-sm rounded-circle bg-soft-info align-self-center">
                                <span class="avatar-title bg-info rounded-circle fs-3">
                                    <i class="bx bx-phone-call text-white"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Today -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100 bordered">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">Sales Today</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="{{ $stats['sales_today'] ?? 0 }}">{{ $stats['sales_today'] ?? 0 }}</span>
                            </h4>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <div class="avatar-sm rounded-circle bg-soft-success align-self-center">
                                <span class="avatar-title bg-success rounded-circle fs-3">
                                    <i class="bx bx-check-circle text-white"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MTD Sale -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-h-100 bordered">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <span class="text-muted mb-3 lh-1 d-block text-truncate">MTD Sale</span>
                            <h4 class="mb-3">
                                <span class="counter-value" data-target="{{ $stats['mtd_sales'] ?? 0 }}">{{ $stats['mtd_sales'] ?? 0 }}</span>
                            </h4>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <div class="avatar-sm rounded-circle align-self-center" style="background: rgba(212, 175, 55, 0.18);">
                                <span class="avatar-title rounded-circle fs-3" style="background: var(--gold); color: white;">
                                    <i class="bx bx-trophy"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-2">
        <div class="col-lg-6">
            <div class="card bordered">
                <div class="card-body">
                    <h5 class="card-title">Attendance Summary (This Month)</h5>
                    @php $a = $stats['attendance_summary'] ?? null; @endphp
                    <div class="row text-center mt-3">
                        <div class="col-3">
                            <div class="h4">{{ $a['total_records'] ?? 0 }}</div>
                            <div class="text-muted">Records</div>
                        </div>
                        <div class="col-3">
                            <div class="h4 text-success">{{ $a['present_days'] ?? 0 }}</div>
                            <div class="text-muted">Present</div>
                        </div>
                        <div class="col-3">
                            <div class="h4 text-danger">{{ $a['absent_days'] ?? 0 }}</div>
                            <div class="text-muted">Absent</div>
                        </div>
                        <div class="col-3">
                            <div class="h4">{{ $a['late_days'] ?? 0 }}</div>
                            <div class="text-muted">Late</div>
                        </div>
                    </div>
                    <div class="row text-center mt-3">
                        <div class="col-6">
                            <div class="h5">{{ $a['total_working_hours'] ?? 0 }} hrs</div>
                            <div class="text-muted">Total Hours</div>
                        </div>
                        <div class="col-6">
                            <div class="h5">{{ $a['average_working_hours'] ?? 0 }} hrs</div>
                            <div class="text-muted">Avg/Day</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card bordered">
                <div class="card-body">
                    <h5 class="card-title">Today's Attendance</h5>
                    @php $today = $stats['today_status'] ?? null; @endphp
                    @if($today)
                        <p class="mb-1"><strong>Status:</strong> <span class="badge bg-{{ $today->status === 'present' ? 'success' : ($today->status === 'late' ? 'warning' : 'danger') }}">{{ ucfirst($today->status) }}</span></p>
                        <p class="mb-1"><strong>Login:</strong> {{ $today->formatted_login_time ?? ($today->login_time ? \Carbon\Carbon::parse($today->login_time)->format('g:i A') : 'N/A') }}</p>
                        <p class="mb-1"><strong>Logout:</strong> {{ $today->formatted_logout_time ?? ($today->logout_time ? \Carbon\Carbon::parse($today->logout_time)->format('g:i A') : 'N/A') }}</p>
                    @else
                        <p class="text-muted">No attendance record for today.</p>
                        <a href="#" id="employeeMarkBtn" class="btn btn-gold btn-sm">Mark Attendance</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access Panel -->
    <div class="row">
        <div class="col-12">
            <div class="card bordered">
                <div class="card-body">
                    <h5 class="card-title mb-3">Quick Access</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="{{ route('attendance.dashboard') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="bx bx-calendar-check fs-4 mb-2"></i>
                                <div>My Attendance</div>
                                <small class="text-muted">View my attendance records</small>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('my-dock-records') }}" class="btn btn-outline-warning w-100 py-3">
                                <i class="mdi mdi-cash-minus fs-4 mb-2"></i>
                                <div>My Dock Records</div>
                                <small class="text-muted">View my salary deductions</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card bordered">
                <div class="card-body text-center py-4">
                    <h5 class="mb-3">Ready to Make Calls?</h5>
                    <a href="{{ route('employee.leads') }}" class="btn btn-primary btn-lg">
                        <i class="bx bx-phone me-2"></i> Start Calling Leads
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Chart (Optional) -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card bordered">
                <div class="card-header">
                    <h4 class="card-title mb-0">My Performance This Week</h4>
                </div>
                <div class="card-body">
                    <div class="text-center text-muted py-5">
                        <i class="bx bx-bar-chart fs-1 mb-3"></i>
                        <p>Performance charts coming soon...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        // Counter animation
        document.querySelectorAll('.counter-value').forEach(counter => {
            const target = +counter.getAttribute('data-target');
            const updateCounter = () => {
                const current = +counter.innerText;
                const increment = target / 50;

                if (current < target) {
                    counter.innerText = Math.ceil(current + increment);
                    setTimeout(updateCounter, 20);
                } else {
                    counter.innerText = target;
                }
            };
            updateCounter();
        });
    </script>
        <script>
            document.addEventListener('DOMContentLoaded', function(){
                const empBtn = document.getElementById('employeeMarkBtn');
                if (!empBtn) return;
                empBtn.addEventListener('click', function(e){
                    e.preventDefault();
                    if (!confirm('Mark your attendance now?')) return;
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    fetch('{{ route('attendance.mark-manual.post') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ force_office: 0 })
                    }).then(r => r.json()).then(data => {
                        if (data.success) {
                            alert(data.message || 'Attendance marked');
                            setTimeout(() => location.reload(), 500);
                        } else {
                            alert(data.message || 'Could not mark attendance');
                        }
                    }).catch(err => { console.error(err); alert('Network error'); });
                });
            });
        </script>
@endsection
