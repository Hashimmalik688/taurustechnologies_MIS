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
            <h4><i class="bx bx-cog"></i> Settings</h4>
            <p>System configuration, permissions &amp; tools</p>
        </div>

        <div class="hub-section-label">General</div>
        <div class="hub-grid">
            @canViewModule('settings')
            <a href="{{ route('settings.index') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-slider-alt"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">System Settings</div>
                    <p class="hub-card-desc">Attendance, office networks, notifications &amp; general config</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule

            @hasrole('Super Admin')
            <a href="{{ route('settings.permissions.index') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-shield-alt"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Permissions Manager</div>
                    <p class="hub-card-desc">Role-based permissions &amp; access controls</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endhasrole

            <a href="{{ route('settings.themes') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-palette"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Theme Settings</div>
                    <p class="hub-card-desc">Switch between elegant CRM themes &mdash; glass, dark, blue &amp; more</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
        </div>

        <div class="hub-section-label">Tools</div>
        <div class="hub-grid">
            @canViewModule('reports')
            <a href="{{ route('settings.reports.index') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-bar-chart-alt-2"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Reports</div>
                    <p class="hub-card-desc">Sales, partner, agent &amp; manager reports with CSV export</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule

            @canViewModule('duplicate-checker')
            <a href="{{ route('admin.dupe-checker.index') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-copy-alt"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Duplicate Checker</div>
                    <p class="hub-card-desc">Find &amp; manage duplicate records to keep data clean</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule

            @canViewModule('account-switch-log')
            <a href="{{ route('admin.account-switching-log') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-transfer"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Account Switch Log</div>
                    <p class="hub-card-desc">Audit trail of account impersonation &amp; switching</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule

            @canViewModule('chat-shadow')
            <a href="{{ route('settings.chat-shadow.index') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-show"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Chat Shadowing</div>
                    <p class="hub-card-desc">Monitor &amp; review user conversations in read-only mode</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule
        </div>
    </div>
@endsection

