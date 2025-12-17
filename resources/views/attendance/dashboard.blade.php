@extends('layouts.master')

@section('title', 'My Attendance')

@section('css')
<style>
.attendance-calendar {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.calendar-header h4 {
    margin: 0;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 0.5rem;
}

.calendar-day-header {
    text-align: center;
    font-weight: 600;
    font-size: 0.85rem;
    padding: 0.5rem;
    color: #6b7280;
    background: #f3f4f6;
    border-radius: 4px;
}

.calendar-day {
    aspect-ratio: 1;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 0.5rem;
    text-align: center;
    position: relative;
    cursor: pointer;
    transition: all 0.2s;
    background: white;
}

.calendar-day:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.calendar-day.other-month {
    opacity: 0.3;
}

.calendar-day.today {
    border-color: #d4af37;
    border-width: 3px;
}

.calendar-day.present {
    background: #d1fae5;
    border-color: #10b981;
}

.calendar-day.late {
    background: #fed7aa;
    border-color: #f59e0b;
}

.calendar-day.absent {
    background: #fee2e2;
    border-color: #ef4444;
}

.day-number {
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 0.25rem;
}

.day-status {
    font-size: 0.65rem;
    text-transform: uppercase;
    font-weight: 600;
}

.day-time {
    font-size: 0.6rem;
    color: #6b7280;
    margin-top: 0.25rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat-box {
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
    text-align: center;
}

.stat-box.present {
    border-color: #10b981;
    background: #d1fae5;
}

.stat-box.absent {
    border-color: #ef4444;
    background: #fee2e2;
}

.stat-box.late {
    border-color: #f59e0b;
    background: #fed7aa;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
}

.stat-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    font-weight: 600;
    color: #6b7280;
    margin-top: 0.5rem;
}

.checkin-widget {
    background: white;
    border: 2px solid #d4af37;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    text-align: center;
}

.btn-checkin {
    background: #d4af37;
    color: white;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-checkin:hover {
    background: #b8941f;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(212, 175, 55, 0.3);
}

.btn-checkout {
    background: #6b7280;
    color: white;
}

.btn-checkout:hover {
    background: #4b5563;
}

.today-status {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-weight: 600;
    margin-bottom: 1rem;
}

.today-status.present {
    background: #d1fae5;
    color: #065f46;
}

.today-status.late {
    background: #fed7aa;
    color: #92400e;
}

.today-status.absent {
    background: #fee2e2;
    color: #991b1b;
}
</style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Attendance
        @endslot
        @slot('title')
            My Attendance
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <!-- Check-in/Check-out Widget -->
            <div class="checkin-widget">
                @if($todayAttendance)
                    <div class="today-status {{ $todayAttendance->status }}">
                        <i class="bx bx-check-circle"></i> 
                        {{ ucfirst($todayAttendance->status) }} 
                        - Checked in at {{ $todayAttendance->login_time ? $todayAttendance->login_time->format('h:i A') : 'N/A' }}
                    </div>
                    <br>
                    @if(!$todayAttendance->logout_time)
                        <button id="btnCheckout" class="btn-checkin btn-checkout">
                            <i class="bx bx-log-out"></i> Check Out
                        </button>
                    @else
                        <p class="text-muted">Checked out at {{ $todayAttendance->logout_time->format('h:i A') }}</p>
                        <p class="text-muted">Total hours: {{ $todayAttendance->working_hours ?? 0 }} hrs</p>
                    @endif
                @else
                    <h5>You haven't checked in today</h5>
                    <p class="text-muted">Click below to mark your attendance</p>
                    <button id="btnCheckin" class="btn-checkin">
                        <i class="bx bx-log-in"></i> Check In
                    </button>
                @endif
            </div>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-value">{{ $stats['total_days'] }}</div>
                    <div class="stat-label">Total Days</div>
                </div>
                <div class="stat-box present">
                    <div class="stat-value">{{ $stats['present'] }}</div>
                    <div class="stat-label">Present</div>
                </div>
                <div class="stat-box late">
                    <div class="stat-value">{{ $stats['late'] }}</div>
                    <div class="stat-label">Late</div>
                </div>
                <div class="stat-box absent">
                    <div class="stat-value">{{ $stats['absent'] }}</div>
                    <div class="stat-label">Absent</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $stats['total_hours'] }}</div>
                    <div class="stat-label">Total Hours</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $stats['avg_hours'] }}</div>
                    <div class="stat-label">Avg Hours/Day</div>
                </div>
            </div>

            <!-- Calendar -->
            <div class="attendance-calendar">
                <div class="calendar-header">
                    <button class="btn btn-sm btn-outline-secondary" onclick="window.location.href='?month={{ \Carbon\Carbon::parse($currentMonth)->subMonth()->format('Y-m') }}'">
                        <i class="bx bx-chevron-left"></i> Previous
                    </button>
                    <h4>{{ \Carbon\Carbon::parse($currentMonth)->format('F Y') }}</h4>
                    <button class="btn btn-sm btn-outline-secondary" onclick="window.location.href='?month={{ \Carbon\Carbon::parse($currentMonth)->addMonth()->format('Y-m') }}'">
                        Next <i class="bx bx-chevron-right"></i>
                    </button>
                </div>

                <div class="calendar-grid">
                    <!-- Day headers -->
                    <div class="calendar-day-header">Mon</div>
                    <div class="calendar-day-header">Tue</div>
                    <div class="calendar-day-header">Wed</div>
                    <div class="calendar-day-header">Thu</div>
                    <div class="calendar-day-header">Fri</div>
                    <div class="calendar-day-header">Sat</div>
                    <div class="calendar-day-header">Sun</div>

                    <!-- Calendar days -->
                    @foreach($calendar as $week)
                        @foreach($week as $day)
                            @php
                                $classes = ['calendar-day'];
                                if (!$day['isCurrentMonth']) $classes[] = 'other-month';
                                if ($day['isToday']) $classes[] = 'today';
                                if ($day['attendance']) {
                                    if ($day['attendance']->isLate()) {
                                        $classes[] = 'late';
                                    } else {
                                        $classes[] = $day['attendance']->status;
                                    }
                                }
                            @endphp
                            <div class="{{ implode(' ', $classes) }}" title="{{ $day['attendance'] ? ucfirst($day['attendance']->status) : 'No record' }}">
                                <div class="day-number">{{ $day['date']->format('j') }}</div>
                                @if($day['attendance'])
                                    <div class="day-status">
                                        {{ $day['attendance']->isLate() ? 'Late' : ucfirst($day['attendance']->status) }}
                                    </div>
                                    @if($day['attendance']->login_time)
                                        <div class="day-time">{{ $day['attendance']->login_time->format('H:i') }}</div>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function(){
        const checkin = document.getElementById('btnCheckin');
        const checkout = document.getElementById('btnCheckout');
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        if (!tokenMeta) return;
        const token = tokenMeta.getAttribute('content');

        function post(url, body) {
            return fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(body || {})
            }).then(r => r.json());
        }

        if (checkin) {
            checkin.addEventListener('click', function(e){
                e.preventDefault();
                checkin.disabled = true;
                checkin.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Checking in...';
                
                post('{{ url('/attendance/check-in') }}', { force_office: 0 })
                    .then(data => {
                        if (data.success) {
                            setTimeout(() => location.reload(), 500);
                        } else {
                            alert(data.message || 'Could not check in');
                            checkin.disabled = false;
                            checkin.innerHTML = '<i class="bx bx-log-in"></i> Check In';
                        }
                    }).catch(err => { 
                        console.error(err); 
                        alert('Network error'); 
                        checkin.disabled = false;
                        checkin.innerHTML = '<i class="bx bx-log-in"></i> Check In';
                    });
            });
        }

        if (checkout) {
            checkout.addEventListener('click', function(e){
                e.preventDefault();
                checkout.disabled = true;
                checkout.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Checking out...';
                
                post('{{ url('/attendance/check-out') }}')
                    .then(data => {
                        if (data.success) {
                            setTimeout(() => location.reload(), 500);
                        } else {
                            alert(data.message || 'Could not check out');
                            checkout.disabled = false;
                            checkout.innerHTML = '<i class="bx bx-log-out"></i> Check Out';
                        }
                    }).catch(err => { 
                        console.error(err); 
                        alert('Network error'); 
                        checkout.disabled = false;
                        checkout.innerHTML = '<i class="bx bx-log-out"></i> Check Out';
                    });
            });
        }
    });
</script>
@endsection
