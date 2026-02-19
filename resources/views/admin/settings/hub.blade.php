@extends('layouts.master')

@section('title')
    Settings
@endsection

@section('css')
@include('components.hub-styles')
@endsection

@section('content')
    <div class="hub-page">
        <div class="hub-header">
            <h4><i class="bx bx-cog"></i>Settings</h4>
            <p>Manage your system configuration, permissions, and tools</p>
        </div>

        <div class="hub-section-label">General</div>
        <div class="hub-grid">
            @canViewModule('settings')
            <a href="{{ route('settings.index') }}" class="hub-card">
                <div class="hub-card-icon">
                    <i class="bx bx-slider-alt"></i>
                </div>
                <div class="hub-card-body">
                    <div class="hub-card-title">System Settings</div>
                    <p class="hub-card-desc">Configure attendance, office networks, notifications, and general system behavior</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule

            @hasrole('Super Admin')
            <a href="{{ route('settings.permissions.index') }}" class="hub-card">
                <div class="hub-card-icon">
                    <i class="bx bx-shield-alt"></i>
                </div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Permissions Manager</div>
                    <p class="hub-card-desc">Manage role-based permissions and user access controls across the system</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endhasrole
        </div>

        <div class="hub-section-label">Tools</div>
        <div class="hub-grid">
            @canViewModule('duplicate-checker')
            <a href="{{ route('admin.dupe-checker.index') }}" class="hub-card">
                <div class="hub-card-icon">
                    <i class="bx bx-copy-alt"></i>
                </div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Duplicate Checker</div>
                    <p class="hub-card-desc">Find and manage duplicate records in the system to keep your data clean</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule

            @canViewModule('account-switch-log')
            <a href="{{ route('admin.account-switching-log') }}" class="hub-card">
                <div class="hub-card-icon">
                    <i class="bx bx-transfer"></i>
                </div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Account Switch Log</div>
                    <p class="hub-card-desc">View audit trail of admin account impersonation and switching activity</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule
        </div>
    </div>
@endsection

