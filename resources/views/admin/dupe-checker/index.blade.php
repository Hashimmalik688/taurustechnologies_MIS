@extends('layouts.master')

@section('title', 'Duplicate Checker')

@section('css')
<link href="{{ URL::asset('build/css/app.min.css') }}" rel="stylesheet" type="text/css" />
@include('partials.pipeline-dashboard-styles')
<style>
/* ── Page header ── */
.page-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:.65rem;flex-wrap:wrap;gap:.5rem}
.page-hdr h5{margin:0;font-size:1.1rem;font-weight:700;display:flex;align-items:center;gap:.4rem}
.page-hdr h5 i{color:var(--bs-gold,#d4af37)}
.page-hdr .ph-sub{font-size:.72rem;color:var(--bs-surface-500);margin-left:.15rem}

/* ── Feature tiles grid ── */
.feat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:.75rem;margin-bottom:.75rem}

.feat-tile{padding:0;overflow:hidden;position:relative}
.feat-tile::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:.6rem .6rem 0 0}
.feat-tile.ft-blue::before{background:linear-gradient(90deg,#556ee6,#8b9cf7)}
.feat-tile.ft-green::before{background:linear-gradient(90deg,#34c38f,#6eddb8)}
.feat-tile.ft-warn::before{background:linear-gradient(90deg,#d4af37,#e8c84a)}

.feat-hdr{padding:.75rem .85rem .5rem;display:flex;align-items:center;gap:.55rem}
.feat-icon{width:42px;height:42px;border-radius:.55rem;display:flex;align-items:center;justify-content:center;font-size:1.25rem;flex-shrink:0}
.feat-icon.fi-blue{background:rgba(85,110,230,.08);color:#556ee6}
.feat-icon.fi-green{background:rgba(52,195,143,.08);color:#1a8754}
.feat-icon.fi-warn{background:rgba(212,175,55,.08);color:#b89730}

.feat-title{font-size:.82rem;font-weight:700;margin:0}
.feat-desc{font-size:.68rem;color:var(--bs-surface-500);margin:0;margin-top:.15rem}

.feat-body{padding:.5rem .85rem .85rem}

/* ── File input styled ── */
.f-file{display:block;width:100%;font-size:.72rem;padding:.4rem .55rem;border-radius:12px;border:1px solid var(--bs-surface-200);background:var(--bs-card-bg);color:var(--bs-body-color);transition:border-color .15s,box-shadow .15s;cursor:pointer}
.f-file:focus{outline:none;border-color:#d4af37;box-shadow:0 0 0 3px rgba(212,175,55,.12)}
.f-file::file-selector-button{font-size:.68rem;font-weight:600;padding:.25rem .55rem;border-radius:8px;border:1px solid rgba(212,175,55,.2);background:rgba(212,175,55,.06);color:#b89730;cursor:pointer;margin-right:.5rem;transition:all .15s}
.f-file::file-selector-button:hover{background:rgba(212,175,55,.12)}

/* ── Warning box ── */
.warn-box{display:flex;align-items:flex-start;gap:.45rem;padding:.55rem .7rem;border-radius:.5rem;background:rgba(212,175,55,.06);border:1px solid rgba(212,175,55,.12);font-size:.72rem;color:#b89730;margin-bottom:.65rem}
.warn-box i{font-size:.95rem;flex-shrink:0;margin-top:.05rem}

/* ── How-to section ── */
.howto-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:.75rem;margin-top:.65rem}
.howto-col h6{font-size:.78rem;font-weight:700;display:flex;align-items:center;gap:.3rem;margin-bottom:.4rem}
.howto-col h6 .h-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}
.howto-col ol{font-size:.72rem;color:var(--bs-surface-500);padding-left:1.1rem;margin:0}
.howto-col ol li{margin-bottom:.25rem;line-height:1.45}
</style>
@endsection

@section('content')
<!-- Page Header -->
<div class="page-hdr">
    <h5><i class="bx bx-search-alt-2"></i> Duplicate Checker <span class="ph-sub">Detect & manage duplicate leads</span></h5>
    <a href="{{ route('settings.hub') }}" class="pipe-pill" style="text-decoration:none"><i class="bx bx-arrow-back"></i> Settings</a>
</div>

<!-- Alerts -->
@if(session('success'))
<div style="display:flex;align-items:center;gap:.4rem;padding:.5rem .75rem;border-radius:.5rem;background:rgba(52,195,143,.06);border:1px solid rgba(52,195,143,.12);font-size:.72rem;color:#1a8754;margin-bottom:.65rem">
    <i class="bx bx-check-circle" style="font-size:.95rem"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div style="display:flex;align-items:center;gap:.4rem;padding:.5rem .75rem;border-radius:.5rem;background:rgba(244,106,106,.06);border:1px solid rgba(244,106,106,.12);font-size:.72rem;color:#c84646;margin-bottom:.65rem">
    <i class="bx bx-error-circle" style="font-size:.95rem"></i> {{ session('error') }}
</div>
@endif

<!-- Feature Tiles -->
<div class="feat-grid">
    <!-- Self Check -->
    <div class="ex-card feat-tile ft-blue">
        <div class="feat-hdr">
            <div class="feat-icon fi-blue"><i class="bx bx-data"></i></div>
            <div>
                <h6 class="feat-title">Self-Check Database</h6>
                <p class="feat-desc">Scan all CRM leads for duplicates by phone, SSN, or account number</p>
            </div>
        </div>
        <div class="feat-body">
            <form action="{{ route('admin.dupe-checker.self-check') }}" method="POST">
                @csrf
                <label class="f-label" style="margin-bottom:.3rem">Check By</label>
                <select name="check_by" class="f-input" style="margin-bottom:.55rem;padding:.4rem .55rem;font-size:.72rem">
                    <option value="phone">Phone Number</option>
                    <option value="ssn">SSN</option>
                    <option value="account">Account Number</option>
                    <option value="all">All Fields</option>
                </select>
                <button type="submit" class="pipe-pill-apply" style="width:100%;justify-content:center">
                    <i class="bx bx-search"></i> Run Self-Check
                </button>
            </form>
        </div>
    </div>

    <!-- File Comparison -->
    <div class="ex-card feat-tile ft-green">
        <div class="feat-hdr">
            <div class="feat-icon fi-green"><i class="bx bx-git-compare"></i></div>
            <div>
                <h6 class="feat-title">File Comparison</h6>
                <p class="feat-desc">Upload two files and compare for duplicates by phone number</p>
            </div>
        </div>
        <div class="feat-body">
            <form action="{{ route('admin.dupe-checker.file-comparison') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label class="f-label" style="margin-bottom:.3rem">File 1 (Master Data)</label>
                <input type="file" name="file1" class="f-file" accept=".xlsx,.xls,.csv" required style="margin-bottom:.45rem">
                <label class="f-label" style="margin-bottom:.3rem">File 2 (Check Against)</label>
                <input type="file" name="file2" class="f-file" accept=".xlsx,.xls,.csv" required style="margin-bottom:.55rem">
                <button type="submit" class="pipe-pill-apply" style="width:100%;justify-content:center;background:linear-gradient(135deg,#34c38f,#2ba77a);box-shadow:0 2px 8px rgba(52,195,143,.25)">
                    <i class="bx bx-transfer"></i> Compare Files
                </button>
            </form>
        </div>
    </div>

    <!-- Auto Deduplication -->
    <div class="ex-card feat-tile ft-warn">
        <div class="feat-hdr">
            <div class="feat-icon fi-warn"><i class="bx bx-merge"></i></div>
            <div>
                <h6 class="feat-title">Auto-Deduplicate</h6>
                <p class="feat-desc">Merge duplicate leads by phone and remove extra records</p>
            </div>
        </div>
        <div class="feat-body">
            <div class="warn-box">
                <i class="bx bx-error-circle"></i>
                <div><strong>Warning:</strong> This merges duplicates and deletes extras. This action cannot be undone.</div>
            </div>
            <form action="{{ route('admin.dupe-checker.run-deduplication') }}" method="POST" onsubmit="return confirm('Are you sure you want to run automatic deduplication? This will merge duplicate leads and cannot be undone.');">
                @csrf
                <button type="submit" class="pipe-pill-apply" style="width:100%;justify-content:center;background:linear-gradient(135deg,#d4af37,#c9a227);box-shadow:0 2px 8px rgba(212,175,55,.25)">
                    <i class="bx bx-merge"></i> Run Deduplication
                </button>
            </form>
        </div>
    </div>
</div>

<!-- How to Use -->
<div class="ex-card sec-card">
    <div class="sec-hdr">
        <h6><i class="bx bx-book-open"></i> How to Use</h6>
    </div>
    <div class="sec-body" style="padding:.75rem .85rem">
        <div class="howto-grid">
            <div class="howto-col">
                <h6><span class="h-dot" style="background:#556ee6"></span> Self-Check Database</h6>
                <ol>
                    <li>Select the field to check for duplicates</li>
                    <li>Click "Run Self-Check"</li>
                    <li>Download the CSV report showing all duplicates</li>
                </ol>
            </div>
            <div class="howto-col">
                <h6><span class="h-dot" style="background:#34c38f"></span> File Comparison</h6>
                <ol>
                    <li>Upload File 1 (your master/existing data)</li>
                    <li>Upload File 2 (new data to check)</li>
                    <li>Download CSV with status for each record in File 2</li>
                </ol>
            </div>
            <div class="howto-col">
                <h6><span class="h-dot" style="background:#d4af37"></span> Auto-Deduplicate</h6>
                <ol>
                    <li>Review your data before running</li>
                    <li>Click "Run Deduplication"</li>
                    <li>System merges duplicate records automatically</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('build/libs/toastr/build/toastr.min.js') }}"></script>
@endsection
