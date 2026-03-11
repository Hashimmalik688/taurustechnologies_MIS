@extends('layouts.master')

@section('title')
    Per-Closer Performance
@endsection

@section('css')
    @include('partials.pipeline-dashboard-styles')
    <style>
        .rp-page-hdr { display:flex;align-items:center;justify-content:space-between;margin-bottom:.65rem;flex-wrap:wrap;gap:.5rem }
        .rp-page-hdr h5 { margin:0;font-size:1.1rem;font-weight:700;display:flex;align-items:center;gap:.4rem }
        .rp-page-hdr h5 i { color:var(--bs-gold,#d4af37) }
        .rp-page-hdr .rp-sub { font-size:.72rem;color:var(--bs-surface-500);margin-left:.2rem }

        .rp-empty { text-align:center;padding:3rem 1rem;color:var(--bs-surface-500) }
        .rp-empty i { font-size:2.5rem;display:block;margin-bottom:.5rem;opacity:.25 }
        .rp-empty h6 { font-size:.85rem;font-weight:700;margin-bottom:.25rem }
        .rp-empty p { font-size:.72rem }

        .loading-overlay {
            position:absolute;top:0;left:0;right:0;bottom:0;
            background:rgba(255,255,255,.8);display:flex;align-items:center;
            justify-content:center;z-index:10;border-radius:.55rem;
        }
        .loading-overlay .spinner-border { width:2rem;height:2rem }

        .rp-table { width:100%;border-collapse:separate;border-spacing:0;font-size:.73rem }
        .rp-table thead th {
            padding:.55rem .65rem;font-size:.65rem;font-weight:700;text-transform:uppercase;
            letter-spacing:.5px;color:var(--bs-surface-500,#64748b);
            background:rgba(248,250,252,.9);border-bottom:2px solid rgba(0,0,0,.06);
            white-space:nowrap;position:sticky;top:0;z-index:2;
        }
        .rp-table tbody td {
            padding:.5rem .65rem;border-bottom:1px solid rgba(0,0,0,.035);
            color:var(--bs-surface-900,#1e293b);vertical-align:middle;
        }
        .rp-table tbody tr:hover td { background:rgba(212,175,55,.04) }
        .rp-table tbody tr:last-child td { border-bottom:none }
        .rp-table tfoot td {
            padding:.55rem .65rem;border-top:2px solid rgba(0,0,0,.08);
            color:var(--bs-surface-900,#1e293b);
        }
        .rp-table tfoot tr:hover td { background:rgba(212,175,55,.06) }
        .rp-th-name { min-width:150px }
        .rp-th-num { text-align:right }
        .rp-td-name { font-weight:600 }
        .rp-td-num { text-align:right;font-weight:600;font-variant-numeric:tabular-nums }

        .rp-badge {
            font-size:.6rem;font-weight:700;padding:.15rem .45rem;border-radius:10px;
            display:inline-block;text-transform:uppercase;letter-spacing:.4px;white-space:nowrap;
        }
        .rp-badge-sale     { background:rgba(52,195,143,.12);color:#1a8754 }
        .rp-badge-pending  { background:rgba(241,180,76,.12);color:#b87a14 }
        .rp-badge-declined { background:rgba(244,106,106,.12);color:#c84646 }
        .rp-badge-accepted { background:rgba(80,165,241,.12);color:#2b81c9 }
        .rp-badge-default  { background:rgba(108,117,125,.08);color:#6c757d }

        .rp-results-hdr {
            padding:.6rem .85rem;border-bottom:1px solid rgba(0,0,0,.06);
            display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.4rem;
        }
        .rp-results-hdr h6 { margin:0;font-size:.82rem;font-weight:700;display:flex;align-items:center;gap:.35rem }
        .rp-results-hdr h6 i { color:var(--bs-gold,#d4af37);font-size:.95rem }
        .rp-results-hdr h6 span { font-weight:400;color:var(--bs-surface-500);font-size:.72rem }

        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .rp-table thead th {
            background:rgba(15,23,42,.6);color:#94a3b8;border-bottom-color:rgba(255,255,255,.06);
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .rp-table tbody td {
            color:#e2e8f0;border-bottom-color:rgba(255,255,255,.04);
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .rp-table tfoot td {
            color:#e2e8f0;border-top-color:rgba(255,255,255,.1);
        }
        :is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .loading-overlay {
            background:rgba(15,23,42,.8);
        }
    </style>
@endsection

@section('content')
    <div class="rp-page-hdr">
        <h5>
            <i class="bx bx-phone-call"></i> Per-Closer Performance
            <span class="rp-sub">Dialed &bull; Connected &bull; Disposed &bull; Sales</span>
        </h5>
        <a href="{{ route('settings.reports.hub') }}" class="act-btn a-primary" style="font-size:.72rem;padding:.3rem .65rem">
            <i class="bx bx-arrow-back"></i> Reports
        </a>
    </div>

    {{-- Filters --}}
    <div class="ex-card sec-card" style="margin-bottom:.65rem">
        <div class="sec-body" style="padding:.75rem">
            <div style="display:flex;gap:.55rem;align-items:flex-end;flex-wrap:wrap">
                <div>
                    <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">From <span style="font-weight:400;opacity:.6">(PT)</span></label>
                    <input type="date" id="csDateFrom" style="font-size:.72rem;padding:.3rem .5rem;border:1px solid rgba(0,0,0,.1);border-radius:8px;background:#fff">
                </div>
                <div>
                    <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">To <span style="font-weight:400;opacity:.6">(PT)</span></label>
                    <input type="date" id="csDateTo" style="font-size:.72rem;padding:.3rem .5rem;border:1px solid rgba(0,0,0,.1);border-radius:8px;background:#fff">
                </div>
                <div>
                    <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Closer</label>
                    <select id="csCloserFilter" style="font-size:.72rem;padding:.3rem .5rem;border:1px solid rgba(0,0,0,.1);border-radius:8px;background:#fff;min-width:180px">
                        <option value="">All Closers</option>
                        @foreach($closers as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="pipe-pill-lbl" style="margin-bottom:.2rem;display:block">Team</label>
                    <select id="csTeamFilter" style="font-size:.72rem;padding:.3rem .5rem;border:1px solid rgba(0,0,0,.1);border-radius:8px;background:#fff;min-width:130px">
                        <option value="">All Teams</option>
                        <option value="ravens">Ravens</option>
                        <option value="peregrine">Peregrine</option>
                    </select>
                </div>
                <button type="button" class="pipe-pill-apply" id="csLoadBtn" style="font-size:.72rem;padding:.3rem .75rem">
                    <i class="bx bx-refresh" style="font-size:.8rem;vertical-align:middle;margin-right:.15rem"></i> Load
                </button>
                <button type="button" class="pipe-pill" id="csTodayBtn" style="font-size:.72rem;padding:.3rem .75rem;font-weight:600">
                    <i class="bx bx-calendar-check" style="font-size:.8rem;vertical-align:middle;margin-right:.15rem"></i> Today
                </button>
                <button type="button" class="act-btn a-success" id="csExportBtn" style="font-size:.72rem;padding:.3rem .65rem;margin-left:auto;display:none">
                    <i class="bx bx-download"></i> Export CSV
                </button>
            </div>
        </div>
    </div>

    {{-- Results --}}
    <div class="ex-card sec-card" id="closerStatsCard" style="position:relative">
        <div id="closerStatsContent">
            <div class="rp-empty">
                <i class="bx bx-phone-call"></i>
                <h6>Click "Load" to view per-closer stats</h6>
                <p>Defaults to the current month if no dates selected</p>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csContent   = document.getElementById('closerStatsContent');
    const csCard      = document.getElementById('closerStatsCard');
    const csLoadBtn   = document.getElementById('csLoadBtn');
    const csExportBtn = document.getElementById('csExportBtn');
    const csDateFrom  = document.getElementById('csDateFrom');
    const csDateTo    = document.getElementById('csDateTo');
    const csCloser    = document.getElementById('csCloserFilter');
    const csTeam      = document.getElementById('csTeamFilter');

    csLoadBtn.addEventListener('click', loadCloserStats);

    document.getElementById('csTodayBtn').addEventListener('click', function() {
        const mtNow = new Date(new Date().toLocaleString('en-US', { timeZone: 'America/Los_Angeles' }));
        const y = mtNow.getFullYear();
        const m = String(mtNow.getMonth() + 1).padStart(2, '0');
        const d = String(mtNow.getDate()).padStart(2, '0');
        const today = y + '-' + m + '-' + d;
        csDateFrom.value = today;
        csDateTo.value = today;
        loadCloserStats();
    });

    csExportBtn.addEventListener('click', function() {
        const params = new URLSearchParams();
        if (csDateFrom.value) params.set('cs_date_from', csDateFrom.value);
        if (csDateTo.value)   params.set('cs_date_to', csDateTo.value);
        if (csCloser.value)   params.set('cs_closer', csCloser.value);
        if (csTeam.value)     params.set('cs_team', csTeam.value);
        window.location.href = '{{ route("settings.reports.closer-stats.export") }}?' + params.toString();
    });

    function loadCloserStats() {
        const loader = document.createElement('div');
        loader.className = 'loading-overlay';
        loader.innerHTML = '<div class="spinner-border text-warning"><span class="visually-hidden">Loading…</span></div>';
        csCard.appendChild(loader);

        const params = new URLSearchParams();
        if (csDateFrom.value) params.set('cs_date_from', csDateFrom.value);
        if (csDateTo.value)   params.set('cs_date_to', csDateTo.value);
        if (csCloser.value)   params.set('cs_closer', csCloser.value);
        if (csTeam.value)     params.set('cs_team', csTeam.value);

        fetch('{{ route("settings.reports.closer-stats") }}?' + params.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        })
        .then(r => r.json())
        .then(data => {
            csContent.innerHTML = data.html || '<div class="rp-empty"><i class="bx bx-phone-off"></i><h6>No closer activity found</h6><p>No dial data for the selected date range</p></div>';
            csExportBtn.style.display = (data.rows && data.rows.length > 0) ? '' : 'none';
            initCloserStatsSort();
        })
        .catch(err => {
            csContent.innerHTML = '<div class="rp-empty"><i class="bx bx-error-circle"></i><h6>Error loading stats</h6><p>' + (err.message || 'Something went wrong') + '</p></div>';
            csExportBtn.style.display = 'none';
        })
        .finally(() => {
            const o = csCard.querySelector('.loading-overlay');
            if (o) o.remove();
        });
    }

    function initCloserStatsSort() {
        const table = document.getElementById('closerStatsTable');
        if (!table) return;
        const headers = table.querySelectorAll('thead th');
        headers.forEach((th, colIdx) => {
            th.style.cursor = 'pointer';
            th.addEventListener('click', function() {
                const tbody = table.querySelector('tbody');
                const rowsArr = Array.from(tbody.querySelectorAll('tr'));
                const asc = th.dataset.sortDir !== 'asc';
                th.dataset.sortDir = asc ? 'asc' : 'desc';
                headers.forEach(h => { if (h !== th) delete h.dataset.sortDir; });
                rowsArr.sort((a, b) => {
                    let aVal = a.children[colIdx]?.textContent.trim().replace(/[%,$]/g, '') || '';
                    let bVal = b.children[colIdx]?.textContent.trim().replace(/[%,$]/g, '') || '';
                    const aNum = parseFloat(aVal);
                    const bNum = parseFloat(bVal);
                    if (!isNaN(aNum) && !isNaN(bNum)) return asc ? aNum - bNum : bNum - aNum;
                    return asc ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
                });
                rowsArr.forEach(row => tbody.appendChild(row));
            });
        });
    }
});
</script>
@endsection
