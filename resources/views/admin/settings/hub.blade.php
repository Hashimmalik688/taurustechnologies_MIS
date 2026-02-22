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
            @hasanyrole('Super Admin|Manager|Co-ordinator|CEO')
            <a href="{{ route('settings.reports.index') }}" class="hub-card">
                <div class="hub-card-icon">
                    <i class="bx bx-bar-chart-alt-2"></i>
                </div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Reports</div>
                    <p class="hub-card-desc">Generate sales, partner, agent, and manager reports with advanced filters and CSV export</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endhasanyrole

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

            @hasrole('Super Admin')
            <a href="{{ route('settings.chat-shadow.index') }}" class="hub-card">
                <div class="hub-card-icon">
                    <i class="bx bx-show"></i>
                </div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Chat Shadowing</div>
                    <p class="hub-card-desc">Monitor and review conversations between users in read-only mode</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endhasrole
        </div>
    </div>
@endsection

