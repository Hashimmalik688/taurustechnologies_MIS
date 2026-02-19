@extends('layouts.master')

@section('title')
    HR Operations
@endsection

@section('css')
@include('components.hub-styles')
@endsection

@section('content')
    <div class="hub-page">
        <div class="hub-header">
            <h4><i class="bx bx-user-check"></i>HR Operations</h4>
            <p>Manage employees, attendance, docking, and holidays</p>
        </div>

        <div class="hub-section-label">People</div>
        <div class="hub-grid">
            @hasanyrole(['Super Admin', 'Manager', 'Co-ordinator', 'HR', 'CEO'])
                @canViewModule('ems')
                <a href="{{ route('employee.ems') }}" class="hub-card">
                    <div class="hub-card-icon">
                        <i class="bx bx-id-card"></i>
                    </div>
                    <div class="hub-card-body">
                        <div class="hub-card-title">Employee Management System</div>
                        <p class="hub-card-desc">View and manage employee profiles, roles, and organizational data</p>
                    </div>
                    <i class="bx bx-chevron-right hub-card-arrow"></i>
                </a>
                @endcanViewModule
            @endhasanyrole

            @hasanyrole(['Super Admin', 'Manager', 'Co-ordinator', 'HR', 'CEO'])
                @canViewModule('attendance')
                <a href="{{ route('attendance.index') }}" class="hub-card">
                    <div class="hub-card-icon">
                        <i class="bx bx-time-five"></i>
                    </div>
                    <div class="hub-card-body">
                        <div class="hub-card-title">Attendance</div>
                        <p class="hub-card-desc">Track daily check-ins, attendance reports, and time-off records</p>
                    </div>
                    <i class="bx bx-chevron-right hub-card-arrow"></i>
                </a>
                @endcanViewModule
            @endhasanyrole
        </div>

        <div class="hub-section-label">Operations</div>
        <div class="hub-grid">
            @hasanyrole(['QA', 'HR', 'Super Admin', 'Manager', 'Co-ordinator', 'CEO'])
                @canViewModule('dock')
                <a href="{{ route('dock.index') }}" class="hub-card">
                    <div class="hub-card-icon">
                        <i class="bx bx-dock-top"></i>
                    </div>
                    <div class="hub-card-body">
                        <div class="hub-card-title">Dock Management</div>
                        <p class="hub-card-desc">Manage salary docking records, deductions, and disciplinary adjustments</p>
                    </div>
                    <i class="bx bx-chevron-right hub-card-arrow"></i>
                </a>
                @endcanViewModule
            @endhasanyrole

            @hasanyrole(['HR', 'Super Admin', 'Co-ordinator', 'CEO'])
                @canViewModule('public-holidays')
                <a href="{{ route('admin.public-holidays.index') }}" class="hub-card">
                    <div class="hub-card-icon">
                        <i class="bx bx-calendar"></i>
                    </div>
                    <div class="hub-card-body">
                        <div class="hub-card-title">Public Holidays</div>
                        <p class="hub-card-desc">Configure public holidays and non-working days for attendance calculations</p>
                    </div>
                    <i class="bx bx-chevron-right hub-card-arrow"></i>
                </a>
                @endcanViewModule
            @endhasanyrole
        </div>
    </div>
@endsection
