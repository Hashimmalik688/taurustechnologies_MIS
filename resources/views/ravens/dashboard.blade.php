@extends('layouts.master')

@section('title')
    My Dashboard
@endsection

@section('css')
    <link href="{{ URL::asset('css/light-theme.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Ravens
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
                        <a href="#" id="RavensMarkBtn" class="btn btn-gold btn-sm">Mark Attendance</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card bordered">
                <div class="card-body text-center py-4">
                    <h5 class="mb-3">Ready to Make Calls?</h5>
                    <a href="{{ route('ravens.calling') }}" class="btn btn-primary btn-lg">
                        <i class="bx bx-phone me-2"></i> Start Calling Leads
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- My Sales Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card bordered">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="bx bx-dollar-circle me-2"></i>My Sales Records
                    </h4>
                    <div>
                        <span class="badge bg-success" style="font-size: 1rem; padding: 0.5rem 1rem;">
                            Total: {{ $mySales->total() ?? 0 }}
                        </span>
                    </div>
                </div>

                <div class="card-body">
                    @if(isset($mySales) && $mySales->count() > 0)
                        <!-- Sales Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card border-success">
                                    <div class="card-body text-center">
                                        <h4 class="text-success">{{ $mySales->where('status', 'accepted')->count() }}</h4>
                                        <p class="mb-0 text-muted">Accepted</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-info">
                                    <div class="card-body text-center">
                                        <h4 class="text-info">{{ $mySales->where('status', 'underwritten')->count() }}</h4>
                                        <p class="mb-0 text-muted">Underwritten</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-warning">
                                    <div class="card-body text-center">
                                        <h4 class="text-warning">{{ $mySales->where('status', 'pending')->count() }}</h4>
                                        <p class="mb-0 text-muted">Pending</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-danger">
                                    <div class="card-body text-center">
                                        <h4 class="text-danger">{{ $mySales->where('status', 'declined')->count() }}</h4>
                                        <p class="mb-0 text-muted">Declined</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="60">#</th>
                                        <th>Customer Name</th>
                                        <th>Sale Date</th>
                                        <th>Status</th>
                                        <th>Coverage</th>
                                        <th>Premium</th>
                                        <th>Carrier</th>
                                        <th width="100">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mySales as $index => $sale)
                                        <tr>
                                            <td>{{ $mySales->firstItem() + $index }}</td>
                                            <td>
                                                <strong>{{ $sale->cn_name ?? 'N/A' }}</strong>
                                                @if($sale->phone_number)
                                                    <br><small class="text-muted">{{ $sale->phone_number }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <i class="bx bx-calendar me-1"></i>
                                                {{ $sale->sale_at ? $sale->sale_at->format('M d, Y') : 'N/A' }}
                                            </td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'accepted' => 'success',
                                                        'underwritten' => 'info',
                                                        'pending' => 'warning',
                                                        'declined' => 'danger',
                                                        'chargeback' => 'dark',
                                                    ];
                                                    $color = $statusColors[$sale->status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $color }}">{{ ucfirst($sale->status) }}</span>
                                                @if($sale->qa_status)
                                                    <br><small class="text-muted">QA: {{ $sale->qa_status }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($sale->coverage_amount)
                                                    <strong>${{ number_format($sale->coverage_amount, 0) }}</strong>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($sale->monthly_premium)
                                                    ${{ number_format($sale->monthly_premium, 2) }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>{{ $sale->carrier_name ?? 'N/A' }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('sales.index') }}?search={{ $sale->phone_number }}" 
                                                   class="btn btn-sm btn-primary" 
                                                   title="View in Sales">
                                                    <i class="bx bx-show"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Showing {{ $mySales->firstItem() }} to {{ $mySales->lastItem() }} of {{ $mySales->total() }}
                            </div>
                            <div>{{ $mySales->links() }}</div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bx bx-package fs-1 text-muted"></i>
                            <p class="text-muted mt-3">No sales yet. Start calling to make your first sale!</p>
                            <a href="{{ route('ravens.calling') }}" class="btn btn-primary mt-2">
                                <i class="bx bx-phone-call me-1"></i> Go to Calling System
                            </a>
                        </div>
                    @endif
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
                const empBtn = document.getElementById('RavensMarkBtn');
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
