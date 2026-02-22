@use('App\Support\Roles')
@extends('layouts.master')

@section('title')
    View Lead
@endsection

@section('css')
<style>
/* ═══════════════════════════════════════════════════════
   Lead Detail — Pill-based Modern CRM Profile
   ═══════════════════════════════════════════════════════ */

/* ── Hero Banner ── */
.ld-hero {
    background: var(--bs-card-bg);
    border: 1px solid rgba(255,255,255,.07);
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    margin-bottom: .75rem;
    overflow: hidden;
    position: relative;
}
.ld-hero::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--bs-gold) 0%, var(--bs-gold-dark) 40%, transparent 100%);
}
.ld-hero-inner {
    display: flex; align-items: center; gap: 1rem;
    padding: 1rem 1.25rem;
    flex-wrap: wrap;
}
.ld-avatar {
    width: 52px; height: 52px; min-width: 52px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--bs-gold) 0%, var(--bs-gold-dark) 100%);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.05rem; font-weight: 800; color: #fff;
    letter-spacing: .5px;
    box-shadow: 0 3px 10px rgba(212,175,55,.25);
    flex-shrink: 0;
}
.ld-identity { flex: 1; min-width: 220px; }
.ld-name {
    font-size: 1.15rem; font-weight: 800; margin: 0 0 .35rem;
    color: var(--bs-surface-800);
    display: flex; align-items: center; gap: .5rem; flex-wrap: wrap;
}
/* pill chips under the name */
.ld-pills {
    display: flex; gap: .35rem; flex-wrap: wrap;
}
.ld-pill {
    font-size: .67rem; font-weight: 600;
    padding: .22rem .6rem;
    border-radius: 50px;
    display: inline-flex; align-items: center; gap: .22rem;
    white-space: nowrap;
    background: rgba(var(--bs-surface-rgb, 128,128,128), .06);
    color: var(--bs-surface-500);
    border: 1px solid rgba(var(--bs-surface-rgb, 128,128,128), .08);
}
.ld-pill i { font-size: .8rem; color: var(--bs-gold); }
.ld-pill-status {
    font-weight: 700; text-transform: uppercase; letter-spacing: .3px;
    font-size: .6rem;
}
.ld-pill-status.st-accepted, .ld-pill-status.st-sale {
    background: rgba(52,195,143,.1); color: #1a8754; border-color: rgba(52,195,143,.2);
}
.ld-pill-status.st-pending {
    background: rgba(241,180,76,.1); color: #b87a14; border-color: rgba(241,180,76,.2);
}
.ld-pill-status.st-rejected, .ld-pill-status.st-declined {
    background: rgba(244,106,106,.1); color: #c84646; border-color: rgba(244,106,106,.2);
}
.ld-pill-status.st-closed, .ld-pill-status.st-transferred {
    background: rgba(80,165,241,.1); color: #2b81c9; border-color: rgba(80,165,241,.2);
}
.ld-pill-status.st-chargeback {
    background: rgba(244,106,106,.1); color: #c84646; border-color: rgba(244,106,106,.2);
}
.ld-pill-status.st-underwritten {
    background: rgba(124,105,239,.1); color: #5b49c7; border-color: rgba(124,105,239,.2);
}
.ld-pill-status.st-forwarded {
    background: rgba(85,110,230,.1); color: #556ee6; border-color: rgba(85,110,230,.2);
}

/* Hero action buttons (pills) */
.ld-hero-actions {
    display: flex; gap: .35rem; flex-wrap: wrap;
    align-items: center; flex-shrink: 0;
}
.ld-abtn {
    font-size: .7rem; font-weight: 600;
    padding: .4rem .85rem; border-radius: 50px;
    border: none; cursor: pointer; text-decoration: none;
    display: inline-flex; align-items: center; gap: .3rem;
    transition: all .18s ease; white-space: nowrap;
}
.ld-abtn-call {
    background: linear-gradient(135deg, #34c38f, #2ba77a);
    color: #fff; box-shadow: 0 2px 8px rgba(52,195,143,.3);
}
.ld-abtn-call:hover { box-shadow: 0 4px 14px rgba(52,195,143,.4); color: #fff; transform: translateY(-1px); }
.ld-abtn-print {
    background: rgba(85,110,230,.08); color: #556ee6;
    border: 1px solid rgba(85,110,230,.15);
}
.ld-abtn-print:hover { background: rgba(85,110,230,.15); color: #556ee6; }
.ld-abtn-back {
    background: rgba(var(--bs-surface-rgb, 128,128,128),.05); color: var(--bs-surface-500);
    border: 1px solid rgba(var(--bs-surface-rgb, 128,128,128),.1);
}
.ld-abtn-back:hover { color: var(--bs-surface-700); background: rgba(var(--bs-surface-rgb, 128,128,128),.1); }

/* ── Pipeline Stepper (horizontal pills) ── */
.ld-pipeline {
    background: var(--bs-card-bg);
    border: 1px solid rgba(255,255,255,.07);
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    margin-bottom: .75rem;
    padding: .65rem 1rem;
}
.ld-pipe-lbl {
    font-size: .58rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .7px;
    color: var(--bs-gold-dark); margin-bottom: .5rem;
    display: flex; align-items: center; gap: .3rem;
}
.ld-pipe-lbl i { font-size: .78rem; opacity: .6; }
.ld-stepper {
    display: flex; gap: .3rem; flex-wrap: wrap;
}
.ld-sp {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .3rem .65rem;
    border-radius: 50px;
    font-size: .62rem; font-weight: 600;
    background: rgba(var(--bs-surface-rgb, 128,128,128),.05);
    color: var(--bs-surface-400);
    border: 1px solid rgba(var(--bs-surface-rgb, 128,128,128),.08);
    transition: all .2s;
    white-space: nowrap;
}
.ld-sp i.sp-icon { font-size: .72rem; opacity: .45; }
.ld-sp .sp-check { display: none; }

/* Done step */
.ld-sp.s-done {
    background: rgba(212,175,55,.1);
    color: var(--bs-gold-dark);
    border-color: rgba(212,175,55,.25);
}
.ld-sp.s-done i.sp-icon { color: var(--bs-gold); opacity: .8; }
.ld-sp.s-done .sp-check { display: inline; font-size: .6rem; color: var(--bs-gold); }

/* Current step */
.ld-sp.s-current {
    background: rgba(52,195,143,.1);
    color: #1a8754;
    border-color: rgba(52,195,143,.25);
    animation: spGlow 2.5s ease-in-out infinite;
}
.ld-sp.s-current i.sp-icon { color: #34c38f; opacity: .9; }
.ld-sp.s-current .sp-check { display: inline; font-size: .6rem; color: #34c38f; }

/* Future step */
.ld-sp.s-future {
    opacity: .55;
    font-style: italic;
}

@keyframes spGlow {
    0%,100% { box-shadow: 0 0 0 0 rgba(52,195,143,.15); }
    50% { box-shadow: 0 0 0 4px rgba(52,195,143,0); }
}

/* ── Info Cards ── */
.ld-card {
    background: var(--bs-card-bg);
    border: 1px solid rgba(255,255,255,.07);
    border-radius: 12px;
    box-shadow: 0 1px 6px rgba(0,0,0,.04);
    margin-bottom: .6rem;
    overflow: hidden;
    transition: box-shadow .2s;
}
.ld-card:hover { box-shadow: 0 4px 18px rgba(0,0,0,.07); }
.ld-card-hdr {
    display: flex; align-items: center; gap: .4rem;
    padding: .5rem .9rem;
    border-bottom: 1px solid rgba(212,175,55,.08);
    background: linear-gradient(90deg, rgba(212,175,55,.04) 0%, transparent 50%);
}
.ld-card-hdr h5 {
    margin: 0; font-size: .76rem; font-weight: 700;
    color: var(--bs-gold-dark);
    display: flex; align-items: center; gap: .35rem;
}
.ld-card-hdr h5 i { font-size: .92rem; opacity: .55; }
.ld-card-body { padding: .6rem .9rem; }

/* ── Field Grid ── */
.ld-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0 1rem;
}
.ld-grid.g3 { grid-template-columns: 1fr 1fr 1fr; }
.ld-grid.g1 { grid-template-columns: 1fr; }

.ld-f {
    padding: .4rem 0;
    border-bottom: 1px solid rgba(var(--bs-surface-rgb, 128,128,128),.04);
}
.ld-f:last-child { border-bottom: none; }
.ld-f.full { grid-column: 1 / -1; }

.ld-fl {
    display: block;
    font-size: .56rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .6px;
    color: var(--bs-surface-400);
    margin-bottom: .12rem;
}
.ld-fv {
    display: block;
    font-size: .8rem; font-weight: 500;
    color: var(--bs-surface-700);
    word-wrap: break-word;
}
.ld-fv.empty, .ld-fv .empty {
    color: var(--bs-surface-muted);
    font-style: italic; font-size: .75rem;
}

/* Pill-shaped badges inside field values */
.ld-badge {
    display: inline-flex; align-items: center; gap: .2rem;
    font-size: .68rem; font-weight: 600;
    padding: .18rem .55rem;
    border-radius: 50px;
}
.ld-badge-green { background: rgba(52,195,143,.1); color: #1a8754; }
.ld-badge-blue  { background: rgba(80,165,241,.1); color: #2b81c9; }
.ld-badge-gold  { background: rgba(212,175,55,.1); color: #b8972e; }
.ld-badge-warn  { background: rgba(241,180,76,.1); color: #b87a14; }
.ld-badge-red   { background: rgba(244,106,106,.1); color: #c84646; }
.ld-badge-purple { background: rgba(124,105,239,.1); color: #5b49c7; }
.ld-badge-muted { background: rgba(var(--bs-surface-rgb, 128,128,128),.06); color: var(--bs-surface-500); }

/* Dividers & sub-headers */
.ld-sep {
    grid-column: 1 / -1;
    border-top: 1px solid rgba(var(--bs-surface-rgb, 128,128,128),.07);
    margin: .3rem 0;
}
.ld-sub {
    grid-column: 1 / -1;
    font-size: .64rem; font-weight: 700;
    color: var(--bs-surface-500);
    padding: .2rem 0 .05rem;
    display: flex; align-items: center; gap: .25rem;
}
.ld-sub i { font-size: .78rem; opacity: .5; }

/* ── Two-column layout ── */
.ld-cols {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0 .7rem;
    align-items: start;
}

/* ── Responsive ── */
@media (max-width: 991px) {
    .ld-cols { grid-template-columns: 1fr; }
    .ld-stepper { gap: .25rem; }
    .ld-grid.g3 { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 575px) {
    .ld-hero-inner { flex-direction: column; align-items: flex-start; }
    .ld-grid { grid-template-columns: 1fr; }
}
</style>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size:.82rem;">
            <i class="mdi mdi-check-all me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ══════════════════════════════════════════
         Pipeline step logic
         ══════════════════════════════════════════ --}}
    @php
        $steps = [
            ['key'=>'sale',      'label'=>'Sale Made',            'icon'=>'mdi-handshake'],
            ['key'=>'submit',    'label'=>'Submitted',            'icon'=>'mdi-file-upload'],
            ['key'=>'issuance',  'label'=>'Policy Issuance',      'icon'=>'mdi-file-document-check'],
            ['key'=>'followup',  'label'=>'Client Follow-up',     'icon'=>'mdi-phone-in-talk'],
            ['key'=>'banking',   'label'=>'Banking Verified',     'icon'=>'mdi-bank-check'],
            ['key'=>'draft',     'label'=>'Draft Confirmation',   'icon'=>'mdi-check-circle',   'future'=>true],
            ['key'=>'commission','label'=>'Commission',           'icon'=>'mdi-currency-usd',   'future'=>true],
            ['key'=>'paid',      'label'=>'Paid',                 'icon'=>'mdi-cash-check',     'future'=>true],
            ['key'=>'recovery',  'label'=>'Advance Recovery',     'icon'=>'mdi-refresh',        'future'=>true],
        ];

        $done = [];
        $isSale = in_array($insurance->status, ['sale','accepted']);
        if ($isSale) { $done[] = 'sale'; }
        if ($insurance->status === 'underwritten' || $isSale) {
            $done[] = 'sale'; $done[] = 'submit';
        }
        $hasIssuance = ($insurance->policy_number || $insurance->issued_policy_number) && ($insurance->partner_id || $insurance->assigned_partner);
        if ($hasIssuance) {
            $done[] = 'sale'; $done[] = 'submit'; $done[] = 'issuance';
        }
        $hasFollowup = in_array($insurance->followup_status, ['Yes','No','Completed','yes','no','completed']) && ($insurance->assigned_followup_person || $insurance->followup_assigned_by);
        if ($hasFollowup) {
            $done[] = 'sale'; $done[] = 'submit'; $done[] = 'issuance'; $done[] = 'followup';
        }
        $hasBV = in_array(strtolower($insurance->bank_verification_status ?? ''), ['bv verified','verified']);
        if ($hasBV) {
            $done[] = 'sale'; $done[] = 'submit'; $done[] = 'issuance'; $done[] = 'followup'; $done[] = 'banking';
        }
        $done = array_unique($done);
        $currentStep = null;
        foreach ($steps as $s) {
            if (!in_array($s['key'], $done)) { $currentStep = $s['key']; break; }
        }
    @endphp

    {{-- ══ HERO BANNER ══ --}}
    <div class="ld-hero">
        <div class="ld-hero-inner">
            {{-- Avatar --}}
            <div class="ld-avatar">
                {{ strtoupper(substr($insurance->cn_name ?? 'U', 0, 1)) }}{{ strtoupper(substr(strstr($insurance->cn_name ?? '', ' ') ?: '', 1, 1)) }}
            </div>
            {{-- Identity --}}
            <div class="ld-identity">
                <h1 class="ld-name">
                    {{ $insurance->cn_name ?? 'Unnamed Lead' }}
                    @if($insurance->status)
                        <span class="ld-pill ld-pill-status st-{{ strtolower($insurance->status) }}">{{ ucfirst($insurance->status) }}</span>
                    @endif
                </h1>
                <div class="ld-pills">
                    <span class="ld-pill"><i class="mdi mdi-phone"></i> {{ $insurance->phone_number ?? 'No phone' }}</span>
                    @if($insurance->secondary_phone_number)
                        <span class="ld-pill"><i class="mdi mdi-phone-plus"></i> {{ $insurance->secondary_phone_number }}</span>
                    @endif
                    <span class="ld-pill"><i class="mdi mdi-map-marker"></i> {{ $insurance->state ?? 'N/A' }} {{ $insurance->zip_code ?? '' }}</span>
                    <span class="ld-pill"><i class="mdi mdi-clock-outline"></i> {{ $insurance->created_at ? $insurance->created_at->format('M d, Y') : 'N/A' }}</span>
                    @if($insurance->team)
                        <span class="ld-pill"><i class="mdi mdi-account-group"></i> {{ $insurance->team }}</span>
                    @endif
                </div>
            </div>
            {{-- Actions --}}
            <div class="ld-hero-actions">
                <button onclick="makeZoomCall()" class="ld-abtn ld-abtn-call"><i class="mdi mdi-phone"></i> Call Now</button>
                <a href="{{ route('sales.prettyPrint', $insurance->id) }}" class="ld-abtn ld-abtn-print" target="_blank"><i class="mdi mdi-printer"></i> Print</a>
                <a href="{{ route('leads.index') }}" class="ld-abtn ld-abtn-back"><i class="mdi mdi-arrow-left"></i> Back</a>
            </div>
        </div>
    </div>

    {{-- ══ PIPELINE STEPPER (horizontal pills) ══ --}}
    <div class="ld-pipeline">
        <div class="ld-pipe-lbl"><i class="mdi mdi-timeline-check"></i> Live & Health Pipeline</div>
        <div class="ld-stepper">
            @foreach($steps as $step)
                @php
                    $isDone = in_array($step['key'], $done);
                    $isCurr = $step['key'] === $currentStep;
                    $isFuture = !empty($step['future']);
                    $cls = $isDone ? 's-done' : ($isCurr ? 's-current' : ($isFuture ? 's-future' : ''));
                @endphp
                <span class="ld-sp {{ $cls }}">
                    <span class="sp-check">
                        @if($isDone) <i class="mdi mdi-check-bold"></i>
                        @elseif($isCurr) <i class="mdi mdi-dots-horizontal"></i>
                        @endif
                    </span>
                    <i class="mdi {{ $step['icon'] }} sp-icon"></i>
                    {{ $step['label'] }}
                </span>
            @endforeach
        </div>
    </div>

    {{-- ══ INFO CARDS — Two columns ══ --}}
    <div class="ld-cols">
        {{-- ── LEFT COLUMN ── --}}
        <div>
            {{-- Personal Information --}}
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-account"></i> Personal Information</h5></div>
                <div class="ld-card-body">
                    <div class="ld-grid">
                        <div class="ld-f">
                            <span class="ld-fl">Full Name</span>
                            <span class="ld-fv {{ $insurance->cn_name ? '' : 'empty' }}">{{ $insurance->cn_name ?? 'Not provided' }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Date of Birth</span>
                            <span class="ld-fv {{ $insurance->date_of_birth ? '' : 'empty' }}">{{ $insurance->date_of_birth ? \Carbon\Carbon::parse($insurance->date_of_birth)->format('M d, Y') : 'Not provided' }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Age</span>
                            <span class="ld-fv {{ $insurance->age ? '' : 'empty' }}">{{ $insurance->age ?? 'N/A' }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Gender</span>
                            <span class="ld-fv">
                                @if($insurance->gender)
                                    <span class="ld-badge ld-badge-blue">{{ $insurance->gender }}</span>
                                @else
                                    <span class="empty">Not provided</span>
                                @endif
                            </span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Birth Place</span>
                            <span class="ld-fv {{ $insurance->birth_place ? '' : 'empty' }}">{{ $insurance->birth_place ?? 'Not provided' }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">SSN</span>
                            <span class="ld-fv {{ $insurance->ssn ? '' : 'empty' }}">{{ $insurance->ssn ?? 'Not provided' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contact Information --}}
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-phone-in-talk"></i> Contact Information</h5></div>
                <div class="ld-card-body">
                    <div class="ld-grid">
                        <div class="ld-f">
                            <span class="ld-fl">Primary Phone</span>
                            <span class="ld-fv {{ $insurance->phone_number ? '' : 'empty' }}">{{ $insurance->phone_number ?? 'Not provided' }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Secondary Phone</span>
                            <span class="ld-fv {{ $insurance->secondary_phone_number ? '' : 'empty' }}">{{ $insurance->secondary_phone_number ?? 'Not provided' }}</span>
                        </div>
                        <div class="ld-f full">
                            <span class="ld-fl">Address</span>
                            <span class="ld-fv {{ $insurance->address ? '' : 'empty' }}">{{ $insurance->address ?? 'Not provided' }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">State</span>
                            <span class="ld-fv {{ $insurance->state ? '' : 'empty' }}">{{ $insurance->state ?? 'Not provided' }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Zip Code</span>
                            <span class="ld-fv {{ $insurance->zip_code ? '' : 'empty' }}">{{ $insurance->zip_code ?? 'Not provided' }}</span>
                        </div>
                        <div class="ld-f full">
                            <span class="ld-fl">Emergency Contact</span>
                            <span class="ld-fv {{ $insurance->emergency_contact ? '' : 'empty' }}">{{ $insurance->emergency_contact ?? 'Not provided' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Health Information --}}
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-heart-pulse"></i> Health Information</h5></div>
                <div class="ld-card-body">
                    <div class="ld-grid">
                        <div class="ld-f">
                            <span class="ld-fl">Nicotine User</span>
                            <span class="ld-fv">
                                @if($insurance->smoker !== null)
                                    <span class="ld-badge {{ $insurance->smoker ? 'ld-badge-warn' : 'ld-badge-green' }}">{{ $insurance->smoker ? 'Yes' : 'No' }}</span>
                                @else
                                    <span class="empty">Not provided</span>
                                @endif
                            </span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Height</span>
                            <span class="ld-fv {{ $insurance->height ? '' : 'empty' }}">{{ $insurance->height ?? 'Not provided' }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Weight</span>
                            <span class="ld-fv {{ $insurance->weight ? '' : 'empty' }}">{{ $insurance->weight ? $insurance->weight . ' lbs' : 'Not provided' }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Driving License</span>
                            <span class="ld-fv">
                                @if($insurance->driving_license !== null)
                                    <span class="ld-badge ld-badge-blue">{{ $insurance->driving_license ? 'Yes' : 'No' }}</span>
                                @else
                                    <span class="empty">Not provided</span>
                                @endif
                            </span>
                        </div>
                        @if($insurance->driving_license_number)
                        <div class="ld-f full">
                            <span class="ld-fl">DL Number</span>
                            <span class="ld-fv">{{ $insurance->driving_license_number }}</span>
                        </div>
                        @endif
                        <div class="ld-f full">
                            <span class="ld-fl">Medical Issues</span>
                            <span class="ld-fv {{ $insurance->medical_issue ? '' : 'empty' }}">{{ $insurance->medical_issue ?? 'None reported' }}</span>
                        </div>
                        <div class="ld-f full">
                            <span class="ld-fl">Medications</span>
                            <span class="ld-fv {{ $insurance->medications ? '' : 'empty' }}">{{ $insurance->medications ?? 'None reported' }}</span>
                        </div>
                        <div class="ld-sep"></div>
                        <div class="ld-sub"><i class="mdi mdi-doctor"></i> Primary Care Physician</div>
                        <div class="ld-f full">
                            <span class="ld-fl">Doctor Name</span>
                            <span class="ld-fv {{ $insurance->doctor_name ? '' : 'empty' }}">{{ $insurance->doctor_name ?? 'Not provided' }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Doctor Phone</span>
                            <span class="ld-fv {{ $insurance->doctor_number ? '' : 'empty' }}">{{ $insurance->doctor_number ?? 'Not provided' }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Doctor Address</span>
                            <span class="ld-fv {{ $insurance->doctor_address ? '' : 'empty' }}">{{ $insurance->doctor_address ?? 'Not provided' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Policy Information --}}
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-shield-check"></i> Policy Information</h5></div>
                <div class="ld-card-body">
                    <div class="ld-grid">
                        <div class="ld-f">
                            <span class="ld-fl">Plan Type</span>
                            <span class="ld-fv {{ $insurance->policy_type ? '' : 'empty' }}">{{ $insurance->policy_type ?? 'Not provided' }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Policy Number</span>
                            <span class="ld-fv {{ $insurance->policy_number ? '' : 'empty' }}">{{ $insurance->policy_number ?? 'Not assigned' }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Carrier Name</span>
                            <span class="ld-fv {{ ($insurance->insuranceCarrier || $insurance->carrier_name) ? '' : 'empty' }}">
                                @if($insurance->insuranceCarrier)
                                    {{ $insurance->insuranceCarrier->name }}
                                @else
                                    {{ $insurance->carrier_name ?? 'Not provided' }}
                                @endif
                            </span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Coverage Amount</span>
                            <span class="ld-fv">
                                @if($insurance->coverage_amount)
                                    <span class="ld-badge ld-badge-blue">${{ number_format($insurance->coverage_amount, 0) }}</span>
                                @else
                                    <span class="empty">Not provided</span>
                                @endif
                            </span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Monthly Premium</span>
                            <span class="ld-fv">
                                @if($insurance->monthly_premium)
                                    <span class="ld-badge ld-badge-green">${{ number_format($insurance->monthly_premium, 2) }}/mo</span>
                                @else
                                    <span class="empty">Not provided</span>
                                @endif
                            </span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Initial Draft Date</span>
                            <span class="ld-fv {{ $insurance->initial_draft_date ? '' : 'empty' }}">{{ $insurance->initial_draft_date ? \Carbon\Carbon::parse($insurance->initial_draft_date)->format('M d, Y') : 'Not set' }}</span>
                        </div>
                        <div class="ld-f full">
                            <span class="ld-fl">Future Draft Date</span>
                            <span class="ld-fv {{ $insurance->future_draft_date ? '' : 'empty' }}">{{ $insurance->future_draft_date ? \Carbon\Carbon::parse($insurance->future_draft_date)->format('M d, Y') : 'Not set' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Beneficiary Information --}}
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-account-heart"></i> Beneficiary Information</h5></div>
                <div class="ld-card-body">
                    @php
                        $beneficiaries = $insurance->beneficiaries ?? [];
                        if (is_string($beneficiaries)) {
                            $decoded = json_decode($beneficiaries, true);
                            $beneficiaries = is_array($decoded) ? $decoded : [];
                        }
                        if (!is_array($beneficiaries)) { $beneficiaries = []; }
                        if (empty($beneficiaries) && ($insurance->beneficiary || $insurance->beneficiary_dob)) {
                            $beneficiaries = [[
                                'name' => $insurance->beneficiary ?? '',
                                'dob' => $insurance->beneficiary_dob ?? '',
                                'relation' => ''
                            ]];
                        }
                    @endphp
                    @if(!empty($beneficiaries))
                        @foreach($beneficiaries as $index => $beneficiary)
                            @if(count($beneficiaries) > 1)
                                <div style="font-size:.68rem; font-weight:700; color:var(--bs-gold-dark); margin-bottom:.25rem;">Beneficiary {{ $index + 1 }}</div>
                            @endif
                            <div class="ld-grid g3" style="{{ !$loop->last ? 'margin-bottom:.5rem; padding-bottom:.5rem; border-bottom:1px solid rgba(var(--bs-surface-rgb,128,128,128),.06);' : '' }}">
                                <div class="ld-f">
                                    <span class="ld-fl">Name</span>
                                    <span class="ld-fv {{ !empty($beneficiary['name']) ? '' : 'empty' }}">{{ $beneficiary['name'] ?? 'Not provided' }}</span>
                                </div>
                                <div class="ld-f">
                                    <span class="ld-fl">Relation</span>
                                    <span class="ld-fv {{ !empty($beneficiary['relation']) ? '' : 'empty' }}">{{ $beneficiary['relation'] ?? 'Not provided' }}</span>
                                </div>
                                <div class="ld-f">
                                    <span class="ld-fl">Date of Birth</span>
                                    <span class="ld-fv {{ !empty($beneficiary['dob']) ? '' : 'empty' }}">{{ !empty($beneficiary['dob']) ? \Carbon\Carbon::parse($beneficiary['dob'])->format('M d, Y') : 'Not provided' }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <span class="ld-fv empty">No beneficiaries added</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── RIGHT COLUMN ── --}}
        <div>
            {{-- Status & Assignment --}}
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-clipboard-check"></i> Status & Assignment</h5></div>
                <div class="ld-card-body">
                    <div class="ld-grid">
                        <div class="ld-f">
                            <span class="ld-fl">Lead Status</span>
                            <span class="ld-fv">
                                @if($insurance->status)
                                    <span class="ld-pill ld-pill-status st-{{ strtolower($insurance->status) }}">{{ ucfirst($insurance->status) }}</span>
                                @else
                                    <span class="empty">Not set</span>
                                @endif
                            </span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Team</span>
                            <span class="ld-fv {{ $insurance->team ? '' : 'empty' }}">{{ $insurance->team ?? 'Not assigned' }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Closer Name</span>
                            <span class="ld-fv {{ $insurance->closer_name ? '' : 'empty' }}">{{ $insurance->closer_name ?? 'Not assigned' }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Managed By</span>
                            <span class="ld-fv {{ $insurance->managedBy ? '' : 'empty' }}">{{ $insurance->managedBy->name ?? 'Not assigned' }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Assigned Partner</span>
                            <span class="ld-fv {{ $insurance->partner ? '' : 'empty' }}">{{ $insurance->partner->name ?? 'Not assigned' }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Assigned Validator</span>
                            <span class="ld-fv {{ $insurance->assignedValidator ? '' : 'empty' }}">{{ $insurance->assignedValidator->name ?? 'Not assigned' }}</span>
                        </div>
                        @if($insurance->sale_date || $insurance->sale_at)
                        <div class="ld-f full">
                            <span class="ld-fl">Sale Date</span>
                            <span class="ld-fv">
                                @if($insurance->sale_date)
                                    {{ \Carbon\Carbon::parse($insurance->sale_date)->format('M d, Y') }}
                                @elseif($insurance->sale_at)
                                    {{ \Carbon\Carbon::parse($insurance->sale_at)->format('M d, Y') }}
                                @endif
                            </span>
                        </div>
                        @endif
                        @if($insurance->decline_reason || $insurance->pending_reason)
                        <div class="ld-f full">
                            <span class="ld-fl">Status Reason</span>
                            <span class="ld-fv">{{ $insurance->decline_reason ?? $insurance->pending_reason ?? 'N/A' }}</span>
                        </div>
                        @endif
                    </div>
                    <div class="ld-sep"></div>
                    <div class="ld-grid g3">
                        <div class="ld-f">
                            <span class="ld-fl">Lead Source</span>
                            <span class="ld-fv {{ $insurance->source ? '' : 'empty' }}">{{ $insurance->source ?? 'Not provided' }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Preset Line</span>
                            <span class="ld-fv {{ $insurance->preset_line ? '' : 'empty' }}">{{ $insurance->preset_line ?? 'Not provided' }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Comments</span>
                            <span class="ld-fv {{ $insurance->comments ? '' : 'empty' }}">{{ $insurance->comments ?? 'None' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bank Account Information --}}
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-bank"></i> Bank Account</h5></div>
                <div class="ld-card-body">
                    <div class="ld-grid">
                        <div class="ld-f">
                            <span class="ld-fl">Bank Name</span>
                            <span class="ld-fv {{ $insurance->bank_name ? '' : 'empty' }}">{{ $insurance->bank_name ?? 'Not provided' }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Account Type</span>
                            <span class="ld-fv {{ $insurance->account_type ? '' : 'empty' }}">{{ $insurance->account_type ?? 'Not provided' }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Account Title</span>
                            <span class="ld-fv {{ $insurance->account_title ? '' : 'empty' }}">{{ $insurance->account_title ?? 'Not provided' }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Bank Balance</span>
                            <span class="ld-fv">
                                @if($insurance->bank_balance)
                                    <span class="ld-badge ld-badge-blue">${{ number_format($insurance->bank_balance, 2) }}</span>
                                @else
                                    <span class="empty">Not provided</span>
                                @endif
                            </span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Routing Number</span>
                            <span class="ld-fv {{ $insurance->routing_number ? '' : 'empty' }}">{{ $insurance->routing_number ?? 'Not provided' }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Account Number</span>
                            <span class="ld-fv {{ $insurance->acc_number ? '' : 'empty' }}">{{ $insurance->acc_number ?? 'Not provided' }}</span>
                        </div>
                    </div>
                    @if($insurance->account_verified_by)
                    <div class="ld-sep"></div>
                    <div class="ld-grid g1">
                        <div class="ld-f">
                            <span class="ld-fl">Verified By</span>
                            <span class="ld-fv">{{ $insurance->account_verified_by }}</span>
                        </div>
                    </div>
                    @endif
                    @if($insurance->ss_amount || $insurance->ss_date)
                    <div class="ld-sep"></div>
                    <div class="ld-grid">
                        <div class="ld-f">
                            <span class="ld-fl">SS Amount</span>
                            <span class="ld-fv {{ $insurance->ss_amount ? '' : 'empty' }}">{{ $insurance->ss_amount ? '$' . number_format($insurance->ss_amount, 2) : 'Not provided' }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">SS Date</span>
                            <span class="ld-fv {{ $insurance->ss_date ? '' : 'empty' }}">{{ $insurance->ss_date ? \Carbon\Carbon::parse($insurance->ss_date)->format('M d, Y') : 'Not provided' }}</span>
                        </div>
                    </div>
                    @endif
                    @if($insurance->bank_verification_status || $insurance->bank_verification_notes)
                    <div class="ld-sep"></div>
                    <div class="ld-sub"><i class="mdi mdi-check-decagram"></i> Bank Verification</div>
                    <div class="ld-grid">
                        <div class="ld-f">
                            <span class="ld-fl">Status</span>
                            <span class="ld-fv">
                                @if($insurance->bank_verification_status)
                                    <span class="ld-badge {{ in_array(strtolower($insurance->bank_verification_status), ['verified','bv verified']) ? 'ld-badge-green' : 'ld-badge-warn' }}">{{ ucfirst($insurance->bank_verification_status) }}</span>
                                @else
                                    <span class="empty">Pending</span>
                                @endif
                            </span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Verified By</span>
                            <span class="ld-fv {{ $insurance->bankVerifier ? '' : 'empty' }}">{{ $insurance->bankVerifier->name ?? 'Not assigned' }}</span>
                        </div>
                    </div>
                    @if($insurance->bank_verification_notes)
                    <div class="ld-grid g1">
                        <div class="ld-f">
                            <span class="ld-fl">Notes</span>
                            <span class="ld-fv">{{ $insurance->bank_verification_notes }}</span>
                        </div>
                    </div>
                    @endif
                    @endif
                </div>
            </div>

            {{-- Card Information (Super Admin/Manager Only) --}}
            @hasanyrole([Roles::SUPER_ADMIN, Roles::CEO, Roles::MANAGER, Roles::COORDINATOR])
            @if($insurance->card_number || $insurance->cvv || $insurance->expiry_date)
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-credit-card"></i> Card Information</h5></div>
                <div class="ld-card-body">
                    <div class="ld-grid g3">
                        <div class="ld-f">
                            <span class="ld-fl">Card Number</span>
                            <span class="ld-fv {{ $insurance->card_number ? '' : 'empty' }}">{{ $insurance->card_number ?? 'Not provided' }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">CVV</span>
                            <span class="ld-fv {{ $insurance->cvv ? '' : 'empty' }}">{{ $insurance->cvv ?? 'Not provided' }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Expiry</span>
                            <span class="ld-fv {{ $insurance->expiry_date ? '' : 'empty' }}">{{ $insurance->expiry_date ?? 'Not provided' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endhasanyrole

            {{-- Follow-Up --}}
            @if($insurance->followup_required || $insurance->followup_scheduled_at)
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-calendar-clock"></i> Follow-Up Schedule</h5></div>
                <div class="ld-card-body">
                    <div class="ld-grid">
                        <div class="ld-f">
                            <span class="ld-fl">Required</span>
                            <span class="ld-fv">
                                <span class="ld-badge {{ $insurance->followup_required ? 'ld-badge-warn' : 'ld-badge-green' }}">{{ $insurance->followup_required ? 'Yes' : 'No' }}</span>
                            </span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Assigned To</span>
                            <span class="ld-fv {{ $insurance->followupPerson ? '' : 'empty' }}">{{ $insurance->followupPerson->name ?? 'Not assigned' }}</span>
                        </div>
                        @if($insurance->followup_scheduled_at)
                        <div class="ld-f">
                            <span class="ld-fl">Scheduled</span>
                            <span class="ld-fv">{{ \Carbon\Carbon::parse($insurance->followup_scheduled_at)->format('M d, Y h:i A') }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Status</span>
                            <span class="ld-fv {{ $insurance->followup_status ? '' : 'empty' }}">{{ $insurance->followup_status ?? 'Pending' }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- QA Review --}}
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-check-decagram"></i> QA Review</h5></div>
                <div class="ld-card-body">
                    <div class="ld-grid">
                        <div class="ld-f">
                            <span class="ld-fl">QA Status</span>
                            <span class="ld-fv">
                                @if($insurance->qa_status)
                                    <span class="ld-badge {{ $insurance->qa_status == 'Approved' ? 'ld-badge-green' : ($insurance->qa_status == 'Rejected' ? 'ld-badge-red' : 'ld-badge-warn') }}">{{ $insurance->qa_status }}</span>
                                @else
                                    <span class="empty">Not reviewed</span>
                                @endif
                            </span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Reviewed By</span>
                            <span class="ld-fv {{ $insurance->qaUser ? '' : 'empty' }}">{{ $insurance->qaUser->name ?? 'Not assigned' }}</span>
                        </div>
                        <div class="ld-f full">
                            <span class="ld-fl">QA Notes</span>
                            <span class="ld-fv {{ $insurance->qa_reason ? '' : 'empty' }}">{{ $insurance->qa_reason ?? 'No notes' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Retention --}}
            @if($insurance->retention_status || $insurance->retention_notes)
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-account-reactivate"></i> Retention</h5></div>
                <div class="ld-card-body">
                    <div class="ld-grid">
                        <div class="ld-f">
                            <span class="ld-fl">Status</span>
                            <span class="ld-fv">
                                @if($insurance->retention_status)
                                    <span class="ld-badge ld-badge-blue">{{ $insurance->retention_status }}</span>
                                @else
                                    <span class="empty">N/A</span>
                                @endif
                            </span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Officer</span>
                            <span class="ld-fv {{ $insurance->retentionOfficer ? '' : 'empty' }}">{{ $insurance->retentionOfficer->name ?? 'Not assigned' }}</span>
                        </div>
                        @if($insurance->retained_at)
                        <div class="ld-f">
                            <span class="ld-fl">Retained Date</span>
                            <span class="ld-fv">{{ \Carbon\Carbon::parse($insurance->retained_at)->format('M d, Y') }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Is Rewrite</span>
                            <span class="ld-fv"><span class="ld-badge {{ $insurance->is_rewrite ? 'ld-badge-warn' : 'ld-badge-muted' }}">{{ $insurance->is_rewrite ? 'Yes' : 'No' }}</span></span>
                        </div>
                        @endif
                        @if($insurance->retention_notes)
                        <div class="ld-f full">
                            <span class="ld-fl">Notes</span>
                            <span class="ld-fv">{{ $insurance->retention_notes }}</span>
                        </div>
                        @endif
                        @if($insurance->chargeback_marked_date)
                        <div class="ld-f full">
                            <span class="ld-fl">Chargeback Date</span>
                            <span class="ld-fv" style="color:#c84646;">{{ \Carbon\Carbon::parse($insurance->chargeback_marked_date)->format('M d, Y h:i A') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Issuance --}}
            @if($insurance->issuance_status || $insurance->assigned_agent_id)
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-file-document-check"></i> Issuance</h5></div>
                <div class="ld-card-body">
                    <div class="ld-grid">
                        <div class="ld-f">
                            <span class="ld-fl">Status</span>
                            <span class="ld-fv">
                                @if($insurance->issuance_status)
                                    <span class="ld-badge {{ $insurance->issuance_status == 'issued' ? 'ld-badge-green' : 'ld-badge-warn' }}">{{ ucfirst($insurance->issuance_status) }}</span>
                                @else
                                    <span class="empty">Not set</span>
                                @endif
                            </span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Assigned Agent</span>
                            <span class="ld-fv {{ $insurance->assignedAgent ? '' : 'empty' }}">{{ $insurance->assignedAgent->name ?? 'Not assigned' }}</span>
                        </div>
                        @if($insurance->issued_policy_number)
                        <div class="ld-f">
                            <span class="ld-fl">Issued Policy #</span>
                            <span class="ld-fv">{{ $insurance->issued_policy_number }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Issuance Date</span>
                            <span class="ld-fv {{ $insurance->issuance_date ? '' : 'empty' }}">{{ $insurance->issuance_date ? \Carbon\Carbon::parse($insurance->issuance_date)->format('M d, Y') : 'Not set' }}</span>
                        </div>
                        @endif
                        @if($insurance->issuance_reason)
                        <div class="ld-f full">
                            <span class="ld-fl">Notes</span>
                            <span class="ld-fv">{{ $insurance->issuance_reason }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Revenue & Commission --}}
            @hasanyrole([Roles::SUPER_ADMIN, Roles::CEO, Roles::MANAGER, Roles::COORDINATOR])
            @if($insurance->agent_commission || $insurance->agent_revenue || $insurance->settlement_percentage)
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-currency-usd"></i> Revenue & Commission</h5></div>
                <div class="ld-card-body">
                    <div class="ld-grid g3">
                        <div class="ld-f">
                            <span class="ld-fl">Commission</span>
                            <span class="ld-fv">
                                @if($insurance->agent_commission)
                                    <span class="ld-badge ld-badge-green">${{ number_format($insurance->agent_commission, 2) }}</span>
                                @else
                                    <span class="empty">Not set</span>
                                @endif
                            </span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Revenue</span>
                            <span class="ld-fv">
                                @if($insurance->agent_revenue)
                                    <span class="ld-badge ld-badge-blue">${{ number_format($insurance->agent_revenue, 2) }}</span>
                                @else
                                    <span class="empty">Not set</span>
                                @endif
                            </span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Settlement %</span>
                            <span class="ld-fv {{ $insurance->settlement_percentage ? '' : 'empty' }}">{{ $insurance->settlement_percentage ? $insurance->settlement_percentage . '%' : 'Not set' }}</span>
                        </div>
                    </div>
                    @if($insurance->commission_calculation_notes)
                    <div class="ld-grid g1">
                        <div class="ld-f">
                            <span class="ld-fl">Notes</span>
                            <span class="ld-fv">{{ $insurance->commission_calculation_notes }}</span>
                        </div>
                    </div>
                    @endif
                    @if($insurance->commission_calculated_at)
                    <div class="ld-grid g1">
                        <div class="ld-f">
                            <span class="ld-fl">Calculated At</span>
                            <span class="ld-fv">{{ \Carbon\Carbon::parse($insurance->commission_calculated_at)->format('M d, Y h:i A') }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
            @endhasanyrole

            {{-- Notes --}}
            @hasanyrole([Roles::SUPER_ADMIN, Roles::CEO, Roles::MANAGER, Roles::COORDINATOR])
            @if($insurance->staff_notes || $insurance->manager_notes)
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-note-text"></i> Notes</h5></div>
                <div class="ld-card-body">
                    <div class="ld-grid g1">
                        @if($insurance->staff_notes)
                        <div class="ld-f">
                            <span class="ld-fl">Staff Notes</span>
                            <span class="ld-fv">{{ $insurance->staff_notes }}</span>
                        </div>
                        @endif
                        @if($insurance->manager_notes)
                        <div class="ld-f">
                            <span class="ld-fl">Manager Notes</span>
                            <span class="ld-fv">{{ $insurance->manager_notes }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            @endhasanyrole

            {{-- Timeline --}}
            <div class="ld-card">
                <div class="ld-card-hdr"><h5><i class="mdi mdi-timeline-clock"></i> Timeline</h5></div>
                <div class="ld-card-body">
                    <div class="ld-grid">
                        <div class="ld-f">
                            <span class="ld-fl">Created</span>
                            <span class="ld-fv">{{ $insurance->created_at ? $insurance->created_at->format('M d, Y h:i A') : 'N/A' }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Last Updated</span>
                            <span class="ld-fv">{{ $insurance->updated_at ? $insurance->updated_at->format('M d, Y h:i A') : 'N/A' }}</span>
                        </div>
                    </div>
                    @if($insurance->verified_at)
                    <div class="ld-grid">
                        <div class="ld-f">
                            <span class="ld-fl">Verified At</span>
                            <span class="ld-fv">{{ \Carbon\Carbon::parse($insurance->verified_at)->format('M d, Y h:i A') }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Verified By</span>
                            <span class="ld-fv {{ $insurance->verifier ? '' : 'empty' }}">{{ $insurance->verifier->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                    @endif
                    @if($insurance->validated_at)
                    <div class="ld-grid">
                        <div class="ld-f">
                            <span class="ld-fl">Validated At</span>
                            <span class="ld-fv">{{ \Carbon\Carbon::parse($insurance->validated_at)->format('M d, Y h:i A') }}</span>
                        </div>
                        <div class="ld-f">
                            <span class="ld-fl">Validated By</span>
                            <span class="ld-fv {{ $insurance->validator ? '' : 'empty' }}">{{ $insurance->validator->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                    @endif
                    @if($insurance->transferred_at)
                    <div class="ld-grid g1">
                        <div class="ld-f">
                            <span class="ld-fl">Transferred At</span>
                            <span class="ld-fv">{{ \Carbon\Carbon::parse($insurance->transferred_at)->format('M d, Y h:i A') }}</span>
                        </div>
                    </div>
                    @endif
                    @if($insurance->closed_at)
                    <div class="ld-grid g1">
                        <div class="ld-f">
                            <span class="ld-fl">Closed At</span>
                            <span class="ld-fv">{{ \Carbon\Carbon::parse($insurance->closed_at)->format('M d, Y h:i A') }}</span>
                        </div>
                    </div>
                    @endif
                    @if($insurance->declined_at)
                    <div class="ld-grid g1">
                        <div class="ld-f">
                            <span class="ld-fl">Declined At</span>
                            <span class="ld-fv" style="color:#c84646;">{{ \Carbon\Carbon::parse($insurance->declined_at)->format('M d, Y h:i A') }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function makeZoomCall() {
            const phoneNumber = '{{ $insurance->phone_number ?? '' }}';
            const sanitizedZoomNumber = '{{ Auth::user()->sanitized_zoom_number ?? '' }}';

            if (!phoneNumber) {
                alert('No phone number available for this lead.');
                return;
            }
            if (!sanitizedZoomNumber) {
                alert('You do not have a Zoom phone number configured.');
                return;
            }

            const cleanNumber = phoneNumber.replace(/[\s\-\(\)]/g, '');
            const zoomUrl = `zoomphonenumber://call?to=${cleanNumber}`;
            window.location.href = zoomUrl;
        }
    </script>
@endsection
