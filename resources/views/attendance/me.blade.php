@extends('layouts.master')

@section('title', 'My Attendance')

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
            <div class="card bordered">
                <div class="card-body">
                    <h4 class="card-title">Last 30 Days</h4>
                    <p class="text-muted">Showing daily attendance for the previous 30 days.</p>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Login</th>
                                    <th>Logout</th>
                                    <th>Hours</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($days as $day)
                                    @php $a = $day['attendance']; @endphp
                                    <tr>
                                        <td>{{ 
                                            \Carbon\Carbon::parse($day['date'])->format('M d, Y')
                                        }}</td>
                                        <td>
                                            @if($a)
                                                <span class="badge bg-{{ $a->status === 'present' ? 'success' : ($a->status === 'late' ? 'warning' : 'danger') }}">{{ ucfirst($a->status) }}</span>
                                            @else
                                                <span class="text-muted">No record</span>
                                            @endif
                                        </td>
                                        <td>{{ $a ? ($a->formatted_login_time ?? ($a->login_time?->format('H:i') ?? 'N/A')) : '-' }}</td>
                                        <td>{{ $a ? ($a->formatted_logout_time ?? ($a->logout_time?->format('H:i') ?? 'N/A')) : '-' }}</td>
                                        <td>{{ $a ? ($a->working_hours ?? 0) : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
