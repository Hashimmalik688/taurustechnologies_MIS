@extends('layouts.master')

@section('title') Partner Commission Report @endsection

@section('css')
    <style>
        .rp-page-hdr { display:flex;align-items:center;justify-content:space-between;margin-bottom:.65rem;flex-wrap:wrap;gap:.5rem }
        .rp-page-hdr h5 { margin:0;font-size:1.1rem;font-weight:700;display:flex;align-items:center;gap:.4rem }
        .rp-page-hdr h5 i { color:var(--bs-gold,#d4af37) }
        .rp-page-hdr .rp-sub { font-size:.72rem;color:var(--bs-surface-500);margin-left:.2rem }
        .rp-page-hdr .rp-actions { display:flex;align-items:center;gap:.4rem }

        .pcr-period-nav { display:flex;align-items:center;gap:.6rem;font-size:.78rem }
        .pcr-period-nav a { display:flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:50%;border:1px solid rgba(0,0,0,.1);color:inherit;text-decoration:none;transition:all .15s }
        .pcr-period-nav a:hover { background:rgba(212,175,55,.14);border-color:#d4af37;transform:scale(1.06) }
        .pcr-period-nav .pcr-period-lbl { font-weight:700;min-width:170px;text-align:center;font-size:.85rem }

        /* KPI cards */
        .pcr-kpi-row { display:flex;flex-wrap:wrap;gap:.6rem;margin-bottom:.75rem }
        .pcr-kpi {
            display:flex;align-items:center;gap:.7rem;
            padding:.85rem 1rem;border-radius:.65rem;
            border:1px solid rgba(0,0,0,.08);background:var(--bs-card-bg);
            flex:1;min-width:170px;position:relative;overflow:hidden;
            box-shadow:0 1px 3px rgba(0,0,0,.05);transition:transform .15s,box-shadow .15s;
        }
        .pcr-kpi::before { content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg, var(--kpi-color, #34c38f), var(--kpi-color-light, #52d19c)); }
        .pcr-kpi:hover { transform:translateY(-2px);box-shadow:0 6px 16px rgba(0,0,0,.08) }
        .pcr-kpi-icon { width:38px;height:38px;border-radius:.55rem;display:flex;align-items:center;justify-content:center;font-size:1.15rem;background:var(--kpi-color-light,#52d19c);color:#fff;flex-shrink:0 }
        .pcr-kpi-val { font-size:1.35rem;font-weight:800;line-height:1.1 }
        .pcr-kpi-lbl { font-size:.62rem;font-weight:700;color:var(--bs-surface-500);margin-top:.2rem;text-transform:uppercase;letter-spacing:.4px }

        /* Table */
        .pcr-table { width:100%;border-collapse:separate;border-spacing:0;font-size:.79rem }
        .pcr-table thead th {
            padding:.6rem .75rem;font-size:.63rem;font-weight:700;text-transform:uppercase;
            letter-spacing:.45px;color:#fff;background:linear-gradient(135deg, #1e293b 0%, #334155 100%);
            white-space:nowrap;text-align:right;
        }
        .pcr-table thead th:first-child { text-align:left;border-radius:.5rem 0 0 0 }
        .pcr-table thead th:last-child { text-align:center;border-radius:0 .5rem 0 0;width:50px }
        .pcr-table tbody td { padding:.55rem .75rem;border-bottom:1px solid rgba(0,0,0,.05);vertical-align:middle;text-align:right;font-variant-numeric:tabular-nums }
        .pcr-table tbody td:first-child { text-align:left }
        .pcr-table tbody tr:hover td { background:rgba(212,175,55,.06) }
        .pcr-table tfoot td { padding:.65rem .75rem;border-top:2px solid rgba(0,0,0,.1);font-weight:800;text-align:right;background:var(--bs-surface-100) }
        .pcr-table tfoot td:first-child { text-align:left }

        .pcr-partner-cell { display:flex;align-items:center;gap:.55rem }
        .pcr-avatar {
            width:30px;height:30px;border-radius:50%;flex-shrink:0;
            display:flex;align-items:center;justify-content:center;
            font-size:.68rem;font-weight:800;border:1px solid;
        }
        .pcr-partner-name { font-weight:700;text-decoration:none;color:inherit }
        a.pcr-partner-name.pcr-partner-link { color:#0369a1;transition:color .15s }
        a.pcr-partner-name.pcr-partner-link:hover { color:#d4af37;text-decoration:underline }
        .pcr-partner-name.pcr-unassigned { font-weight:600;font-style:italic;color:var(--bs-surface-400) }
        .pcr-rank { font-size:.85rem;margin-left:.15rem }

        .pcr-comm-cell { display:flex;flex-direction:column;align-items:flex-end;gap:.2rem;min-width:110px }
        .pcr-comm-val { color:#1a8754;font-weight:800 }
        .pcr-comm-bar-track { width:100%;height:4px;background:var(--bs-surface-200);border-radius:2px;overflow:hidden }
        .pcr-comm-bar-fill { height:100%;background:linear-gradient(90deg,#34c38f,#52d19c);border-radius:2px }
        .pcr-comm-pct { font-size:.62rem;color:var(--bs-surface-400);font-weight:600 }

        .pcr-ledger-col { text-align:center !important }
        .pcr-ledger-link { display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:50%;color:#b89730;text-decoration:none;transition:all .15s;font-size:1rem }
        .pcr-ledger-link:hover { background:rgba(212,175,55,.15);color:#8a6d1f;transform:scale(1.08) }
        .pcr-ledger-dash { color:var(--bs-surface-300) }

        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pcr-table tbody td { color:#e2e8f0;border-bottom-color:rgba(255,255,255,.05) }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .pcr-table tfoot td { color:#e2e8f0;border-top-color:rgba(255,255,255,.12) }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) a.pcr-partner-name.pcr-partner-link { color:#7dd3fc }
    </style>
@endsection

@section('content')

    {{-- Page Header --}}
    <div class="rp-page-hdr">
        <h5>
            <i class="bx bx-dollar-circle"></i> Partner Commission Report
            <span class="rp-sub">Est. commission per partner — premium × 9 × rate</span>
        </h5>
        <div class="rp-actions">
            @if($canViewLedger)
            <a href="{{ route('admin.accounting.partner-ledger') }}" class="act-btn a-secondary" style="font-size:.72rem;padding:.3rem .65rem">
                <i class="bx bx-wallet"></i> Partner Ledger
            </a>
            @endif
            <a href="{{ route('dashboard') }}" class="act-btn a-primary" style="font-size:.72rem;padding:.3rem .65rem">
                <i class="bx bx-arrow-back"></i> Dashboard
            </a>
        </div>
    </div>

    {{-- Period Navigator --}}
    <div class="ex-card sec-card" style="margin-bottom:.7rem">
        <div class="sec-body" style="padding:.6rem .9rem;display:flex;justify-content:center">
            <div class="pcr-period-nav">
                <a href="{{ route('settings.reports.partner-commission-report', ['period' => $prevPeriod]) }}" title="Previous period">
                    <i class="bx bx-chevron-left"></i>
                </a>
                <span class="pcr-period-lbl">{{ $periodLabel }}</span>
                @if(!$isCurrentPeriod)
                <a href="{{ route('settings.reports.partner-commission-report', ['period' => $nextPeriod]) }}" title="Next period">
                    <i class="bx bx-chevron-right"></i>
                </a>
                @else
                <span style="width:28px;height:28px;display:inline-block"></span>
                @endif
            </div>
        </div>
    </div>

    {{-- KPI Row --}}
    <div class="pcr-kpi-row">
        <div class="pcr-kpi" style="--kpi-color:#556ee6;--kpi-color-light:#8b9cf7">
            <div class="pcr-kpi-icon"><i class="bx bx-transfer"></i></div>
            <div>
                <div class="pcr-kpi-val" style="color:#556ee6">{{ $totals['sales_count'] }}</div>
                <div class="pcr-kpi-lbl">Total Sales</div>
            </div>
        </div>
        <div class="pcr-kpi" style="--kpi-color:#b89730;--kpi-color-light:#d4af37">
            <div class="pcr-kpi-icon"><i class="bx bx-receipt"></i></div>
            <div>
                <div class="pcr-kpi-val" style="color:#b89730">${{ number_format($totals['premium'], 0) }}</div>
                <div class="pcr-kpi-lbl">Total Premium</div>
            </div>
        </div>
        <div class="pcr-kpi" style="--kpi-color:#34c38f;--kpi-color-light:#52d19c">
            <div class="pcr-kpi-icon"><i class="bx bx-dollar-circle"></i></div>
            <div>
                <div class="pcr-kpi-val" style="color:#1a8754">${{ number_format($totals['commission'], 0) }}</div>
                <div class="pcr-kpi-lbl">Est. Commission</div>
            </div>
        </div>
        <div class="pcr-kpi" style="--kpi-color:#7c3aed;--kpi-color-light:#a78bfa">
            <div class="pcr-kpi-icon"><i class="bx bx-group"></i></div>
            <div>
                <div class="pcr-kpi-val" style="color:#7c3aed">{{ $rows->count() }}</div>
                <div class="pcr-kpi-lbl">Partners</div>
            </div>
        </div>
    </div>

    {{-- Main Table --}}
    <div class="ex-card sec-card">
        <div class="sec-header" style="padding:.65rem .75rem">
            <h6 class="sec-title" style="font-size:.8rem">
                <i class="bx bx-table"></i> Commission by Partner
                <span style="font-weight:400;color:var(--bs-surface-400);margin-left:.3rem">({{ $rows->count() }} partners)</span>
            </h6>
        </div>
        <div class="sec-body" style="padding:0;overflow-x:auto">
            @if($rows->count() > 0)
                @php $palette = ['#556ee6','#34c38f','#f46a6a','#f1b44c','#50a5f1','#7c3aed','#0ea5e9','#ec4899']; @endphp
                <table class="pcr-table">
                    <thead>
                        <tr>
                            <th>Partner</th>
                            <th>Sales</th>
                            <th>Premium</th>
                            <th>Est. Commission</th>
                            <th><i class="bx bx-wallet" title="Ledger"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $i => $row)
                            @php
                                $isUnassigned = empty($row['partner_id']);
                                $initials = collect(preg_split('/\s+/', trim($row['partner_name'])))
                                    ->filter()->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                                $initials = strtoupper($initials ?: '?');
                                $color = $isUnassigned ? '#adb5bd' : $palette[crc32($row['partner_name']) % count($palette)];
                                $pct = $totals['commission'] > 0 ? round($row['commission'] / $totals['commission'] * 100, 1) : 0;
                                $rank = $i === 0 ? '🥇' : ($i === 1 ? '🥈' : ($i === 2 ? '🥉' : null));
                                $canLink = !$isUnassigned && $canViewLedger;
                                $ledgerUrl = $canLink ? route('admin.accounting.partner-ledger.show', ['partnerId' => $row['partner_id'], 'period' => $selectedPeriod]) : null;
                            @endphp
                            <tr>
                                <td>
                                    <div class="pcr-partner-cell">
                                        <span class="pcr-avatar" style="background:{{ $color }}1a;color:{{ $color }};border-color:{{ $color }}55">{{ $initials }}</span>
                                        @if($canLink)
                                            <a href="{{ $ledgerUrl }}" class="pcr-partner-name pcr-partner-link" target="_blank" rel="noopener">{{ $row['partner_name'] }}</a>
                                        @else
                                            <span class="pcr-partner-name {{ $isUnassigned ? 'pcr-unassigned' : '' }}">{{ $row['partner_name'] }}</span>
                                        @endif
                                        @if($rank)<span class="pcr-rank">{{ $rank }}</span>@endif
                                    </div>
                                </td>
                                <td><span class="bd bd-blue">{{ $row['sales_count'] }}</span></td>
                                <td>${{ number_format($row['premium'], 0) }}</td>
                                <td>
                                    <div class="pcr-comm-cell">
                                        <span class="pcr-comm-val">${{ number_format($row['commission'], 0) }}</span>
                                        <div class="pcr-comm-bar-track"><div class="pcr-comm-bar-fill" style="width:{{ $pct }}%"></div></div>
                                        <span class="pcr-comm-pct">{{ $pct }}% of total</span>
                                    </div>
                                </td>
                                <td class="pcr-ledger-col">
                                    @if($canLink)
                                        <a href="{{ $ledgerUrl }}" class="pcr-ledger-link" title="View {{ $row['partner_name'] }}'s ledger for {{ $periodLabel }}" target="_blank" rel="noopener">
                                            <i class="bx bx-wallet"></i>
                                        </a>
                                    @else
                                        <span class="pcr-ledger-dash">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Total</td>
                            <td>{{ $totals['sales_count'] }}</td>
                            <td>${{ number_format($totals['premium'], 0) }}</td>
                            <td style="color:#1a8754">${{ number_format($totals['commission'], 0) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            @else
                <div style="padding:2rem;text-align:center;color:var(--bs-surface-400)">
                    <i class="bx bx-info-circle" style="font-size:2rem"></i>
                    <p style="margin-top:.5rem">No commission data for this period</p>
                </div>
            @endif
        </div>
    </div>

@endsection
