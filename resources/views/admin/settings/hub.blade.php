@extends('layouts.master')

@section('title')
    Settings
@endsection

@section('css')
<style>
    .settings-hub {
        max-width: 900px;
        margin: 0 auto;
    }

    .settings-hub-header {
        margin-bottom: 2rem;
    }

    .settings-hub-header h4 {
        font-weight: 700;
        font-size: 1.4rem;
        color: var(--text-primary, #111827);
        margin: 0 0 6px;
    }

    .settings-hub-header p {
        font-size: 0.88rem;
        color: var(--text-muted, #6b7280);
        margin: 0;
    }

    .settings-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px;
    }

    .settings-card {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        padding: 20px;
        background: var(--bg-panel, #ffffff);
        border-radius: 12px;
        border: 1px solid var(--panel-border, #e6e9ee);
        text-decoration: none !important;
        color: inherit !important;
        transition: all 0.25s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .settings-card:hover {
        border-color: rgba(212, 175, 55, 0.3);
        box-shadow: 0 4px 16px rgba(212, 175, 55, 0.1);
        transform: translateY(-2px);
    }

    .settings-card:hover .settings-card-icon {
        background: linear-gradient(135deg, var(--gold, #d4af37), #b8922e);
        color: #fff;
    }

    .settings-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: rgba(212, 175, 55, 0.1);
        color: var(--gold, #d4af37);
        transition: all 0.25s ease;
    }

    .settings-card-icon i {
        font-size: 24px;
    }

    .settings-card-body {
        flex: 1;
        min-width: 0;
    }

    .settings-card-title {
        font-weight: 600;
        font-size: 0.95rem;
        color: var(--text-primary, #111827);
        margin: 0 0 4px;
    }

    .settings-card-desc {
        font-size: 0.8rem;
        color: var(--text-muted, #6b7280);
        margin: 0;
        line-height: 1.4;
    }

    .settings-card-arrow {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted, #6b7280);
        opacity: 0;
        transition: all 0.25s ease;
    }

    .settings-card:hover .settings-card-arrow {
        opacity: 1;
        right: 12px;
        color: var(--gold, #d4af37);
    }

    .settings-section-label {
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--text-muted, #6b7280);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 24px 0 10px;
        padding-left: 4px;
    }

    .settings-section-label:first-child {
        margin-top: 0;
    }
</style>
@endsection

@section('content')
    <div class="settings-hub">
        <div class="settings-hub-header">
            <h4><i class="bx bx-cog me-2" style="color: var(--gold, #d4af37)"></i>Settings</h4>
            <p>Manage your system configuration, permissions, and tools</p>
        </div>

        <div class="settings-section-label">General</div>
        <div class="settings-grid">
            <a href="{{ route('settings.index') }}" class="settings-card">
                <div class="settings-card-icon">
                    <i class="bx bx-slider-alt"></i>
                </div>
                <div class="settings-card-body">
                    <div class="settings-card-title">System Settings</div>
                    <p class="settings-card-desc">Configure attendance, office networks, notifications, and general system behavior</p>
                </div>
                <i class="bx bx-chevron-right settings-card-arrow"></i>
            </a>

            @hasrole('Super Admin')
            <a href="{{ route('settings.permissions.index') }}" class="settings-card">
                <div class="settings-card-icon">
                    <i class="bx bx-shield-alt"></i>
                </div>
                <div class="settings-card-body">
                    <div class="settings-card-title">Permissions Manager</div>
                    <p class="settings-card-desc">Manage role-based permissions and user access controls across the system</p>
                </div>
                <i class="bx bx-chevron-right settings-card-arrow"></i>
            </a>
            @endhasrole
        </div>

        <div class="settings-section-label">Tools</div>
        <div class="settings-grid">
            <a href="{{ route('admin.dupe-checker.index') }}" class="settings-card">
                <div class="settings-card-icon">
                    <i class="bx bx-copy-alt"></i>
                </div>
                <div class="settings-card-body">
                    <div class="settings-card-title">Duplicate Checker</div>
                    <p class="settings-card-desc">Find and manage duplicate records in the system to keep your data clean</p>
                </div>
                <i class="bx bx-chevron-right settings-card-arrow"></i>
            </a>

            <a href="{{ route('admin.account-switching-log') }}" class="settings-card">
                <div class="settings-card-icon">
                    <i class="bx bx-transfer"></i>
                </div>
                <div class="settings-card-body">
                    <div class="settings-card-title">Account Switch Log</div>
                    <p class="settings-card-desc">View audit trail of admin account impersonation and switching activity</p>
                </div>
                <i class="bx bx-chevron-right settings-card-arrow"></i>
            </a>
        </div>
    </div>
@endsection

