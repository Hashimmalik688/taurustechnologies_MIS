@extends('layouts.master')

@section('title', 'Audit Log #' . $auditLog->id)

@section('css')
@include('partials.pipeline-dashboard-styles')
<style>
/* ── Page header ── */
.page-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:.65rem;flex-wrap:wrap;gap:.5rem}
.page-hdr h5{margin:0;font-size:1.1rem;font-weight:700;display:flex;align-items:center;gap:.4rem}
.page-hdr h5 i{color:var(--bs-gold,#d4af37)}
.page-hdr .ph-sub{font-size:.72rem;color:var(--bs-surface-500);margin-left:.15rem}

/* ── Detail row ── */
.dtl-grid{display:grid;grid-template-columns:1fr 320px;gap:.65rem}
@media(max-width:991px){.dtl-grid{grid-template-columns:1fr}}

/* ── Key-value pair ── */
.kv-row{display:flex;align-items:flex-start;padding:.45rem 0;border-bottom:1px solid var(--bs-surface-100);gap:.5rem}
.kv-row:last-child{border-bottom:none}
.kv-label{flex:0 0 120px;font-size:.68rem;font-weight:700;color:var(--bs-surface-400);text-transform:uppercase;letter-spacing:.3px;padding-top:.1rem}
.kv-value{flex:1;font-size:.75rem;font-weight:500;color:var(--bs-surface-700)}

/* ── Action pill ── */
.action-pill{display:inline-flex;align-items:center;gap:.2rem;padding:.18rem .5rem;border-radius:10px;font-size:.65rem;font-weight:600;background:rgba(59,130,246,.08);border:1px solid rgba(59,130,246,.12);color:#3b82f6}

/* ── IP / code tag ── */
.code-tag{font-family:'Fira Code','Consolas',monospace;font-size:.65rem;padding:.15rem .4rem;background:rgba(212,175,55,.06);border:1px solid rgba(212,175,55,.1);border-radius:6px;color:#b89730;letter-spacing:.3px}

/* ── JSON block ── */
.json-block{background:rgba(15,23,42,.04);border:1px solid rgba(212,175,55,.1);border-radius:.5rem;padding:.75rem;max-height:320px;overflow-y:auto;font-family:'Fira Code','Consolas',monospace;font-size:.68rem;line-height:1.6;color:var(--bs-surface-600);white-space:pre-wrap;word-break:break-word}

/* ── User agent ── */
.ua-block{font-size:.62rem;line-height:1.5;color:var(--bs-surface-400);word-break:break-all;padding:.4rem .55rem;background:rgba(0,0,0,.02);border-radius:.35rem;border:1px solid var(--bs-surface-100)}

/* ── Nav buttons ── */
.nav-link-btn{display:flex;align-items:center;gap:.3rem;padding:.4rem .65rem;border-radius:10px;font-size:.68rem;font-weight:600;text-decoration:none;border:1px solid rgba(212,175,55,.15);color:#b89730;background:rgba(212,175,55,.04);transition:all .15s;width:100%;margin-bottom:.35rem}
.nav-link-btn:hover{background:rgba(212,175,55,.1);border-color:rgba(212,175,55,.3);color:#a08928}
.nav-link-btn i{font-size:.78rem}
</style>
@endsection

@section('content')
<!-- Page Header -->
<div class="page-hdr">
    <h5><i class="bx bx-detail"></i> Audit Log <span class="ph-sub">#{{ $auditLog->id }}</span></h5>
    <a href="{{ route('admin.audit-logs.index') }}" class="act-btn a-primary" style="text-decoration:none">
        <i class="bx bx-arrow-back"></i> Back to Logs
    </a>
</div>

<div class="dtl-grid">
    <!-- Left Column -->
    <div>
        <!-- Action Details -->
        <div class="ex-card sec-card" style="margin-bottom:.65rem">
            <div class="sec-hdr"><h6><i class="bx bx-bolt-circle"></i> Action Details</h6></div>
            <div class="sec-body" style="padding:.55rem .75rem">
                <div class="kv-row">
                    <span class="kv-label">Log ID</span>
                    <span class="kv-value" style="font-weight:700">#{{ $auditLog->id }}</span>
                </div>
                <div class="kv-row">
                    <span class="kv-label">Action</span>
                    <span class="kv-value"><span class="action-pill">{{ ucfirst(str_replace('_', ' ', $auditLog->action)) }}</span></span>
                </div>
                <div class="kv-row">
                    <span class="kv-label">Date & Time</span>
                    <span class="kv-value">
                        <i class="bx bx-time" style="font-size:.72rem;opacity:.4;vertical-align:middle"></i>
                        {{ $auditLog->created_at->format('M d, Y H:i:s') }}
                    </span>
                </div>
                <div class="kv-row">
                    <span class="kv-label">Timestamp</span>
                    <span class="kv-value"><span class="code-tag">{{ $auditLog->created_at->timestamp }}</span></span>
                </div>
            </div>
        </div>

        <!-- User Information -->
        <div class="ex-card sec-card" style="margin-bottom:.65rem">
            <div class="sec-hdr"><h6><i class="bx bx-user-circle"></i> User Information</h6></div>
            <div class="sec-body" style="padding:.55rem .75rem">
                <div class="kv-row">
                    <span class="kv-label">User ID</span>
                    <span class="kv-value">
                        @if ($auditLog->user)
                            <a href="{{ route('users.show', $auditLog->user->id) }}" style="color:#d4af37;font-weight:600;text-decoration:none;font-size:.72rem">
                                <i class="bx bx-link-external" style="font-size:.7rem;vertical-align:middle"></i> {{ $auditLog->user->id }}
                            </a>
                        @else
                            <span style="color:var(--bs-surface-400)">System</span>
                        @endif
                    </span>
                </div>
                <div class="kv-row">
                    <span class="kv-label">Email</span>
                    <span class="kv-value">
                        @if ($auditLog->user)
                            <span class="v-badge" style="font-size:.62rem">{{ $auditLog->user->email }}</span>
                        @else
                            {{ $auditLog->user_email ?? 'System' }}
                        @endif
                    </span>
                </div>
                <div class="kv-row">
                    <span class="kv-label">Name</span>
                    <span class="kv-value">{{ $auditLog->user?->name ?? 'System' }}</span>
                </div>
            </div>
        </div>

        <!-- Affected Model -->
        <div class="ex-card sec-card" style="margin-bottom:.65rem">
            <div class="sec-hdr"><h6><i class="bx bx-data"></i> Affected Model</h6></div>
            <div class="sec-body" style="padding:.55rem .75rem">
                <div class="kv-row">
                    <span class="kv-label">Model Type</span>
                    <span class="kv-value">{{ $auditLog->model ?? '—' }}</span>
                </div>
                <div class="kv-row">
                    <span class="kv-label">Model ID</span>
                    <span class="kv-value">{{ $auditLog->model_id ?? '—' }}</span>
                </div>
                <div class="kv-row">
                    <span class="kv-label">Description</span>
                    <span class="kv-value">{{ $auditLog->description ?? 'No description' }}</span>
                </div>
            </div>
        </div>

        <!-- Changes Made -->
        @if ($auditLog->changes)
        <div class="ex-card sec-card" style="margin-bottom:.65rem">
            <div class="sec-hdr"><h6><i class="bx bx-code-block"></i> Changes Made</h6></div>
            <div class="sec-body" style="padding:.55rem .75rem">
                <div class="json-block">{{ json_encode($auditLog->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</div>
            </div>
        </div>
        @endif
    </div>

    <!-- Right Column -->
    <div>
        <!-- Request Information -->
        <div class="ex-card sec-card" style="margin-bottom:.65rem">
            <div class="sec-hdr"><h6><i class="bx bx-globe"></i> Request Info</h6></div>
            <div class="sec-body" style="padding:.55rem .75rem">
                <div class="kv-row">
                    <span class="kv-label">IP Address</span>
                    <span class="kv-value"><span class="code-tag">{{ $auditLog->ip_address ?? '—' }}</span></span>
                </div>
                <div class="kv-row">
                    <span class="kv-label">Browser</span>
                    <span class="kv-value" style="font-size:.68rem">
                        @if ($auditLog->user_agent)
                            {{ Str::limit($auditLog->user_agent, 50) }}
                        @else
                            <span style="color:var(--bs-surface-400)">—</span>
                        @endif
                    </span>
                </div>
                @if ($auditLog->user_agent)
                <div style="margin-top:.35rem">
                    <span style="font-size:.6rem;font-weight:700;color:var(--bs-surface-400);text-transform:uppercase;letter-spacing:.3px;display:block;margin-bottom:.25rem">Full User Agent</span>
                    <div class="ua-block">{{ $auditLog->user_agent }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Navigation -->
        <div class="ex-card sec-card">
            <div class="sec-hdr"><h6><i class="bx bx-compass"></i> Quick Navigation</h6></div>
            <div class="sec-body" style="padding:.55rem .75rem">
                <a href="{{ route('admin.audit-logs.index', ['action' => $auditLog->action]) }}" class="nav-link-btn">
                    <i class="bx bx-filter-alt"></i> All "{{ ucfirst(str_replace('_', ' ', $auditLog->action)) }}" logs
                </a>
                @if ($auditLog->user)
                <a href="{{ route('admin.audit-logs.index', ['user_id' => $auditLog->user_id]) }}" class="nav-link-btn">
                    <i class="bx bx-user"></i> All logs from {{ $auditLog->user->email }}
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
