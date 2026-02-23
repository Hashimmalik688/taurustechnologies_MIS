@extends('layouts.master')

@section('title')
    Duplicate Checker
@endsection

@section('css')
    <link href="{{ URL::asset('build/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .dupe-card {
            transition: all 0.3s;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .dupe-card:hover {
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }
        .feature-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 28px;
            margin-bottom: 1rem;
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Admin
        @endslot
        @slot('title')
            Duplicate Checker
        @endslot
    @endcomponent

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        <i class="bx bx-search-alt-2 me-2"></i>Duplicate Lead Checker & Management
                    </h4>
                    <p class="text-muted">
                        Detect and manage duplicate leads in your CRM. Check for duplicates within the database or compare external files.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Feature Cards -->
    <div class="row">
        <!-- Self Check -->
        <div class="col-lg-4">
            <div class="card dupe-card h-100">
                <div class="card-body text-center">
                    <div class="feature-icon bg-soft-primary text-primary mx-auto">
                        <i class="bx bx-data"></i>
                    </div>
                    <h5 class="card-title">Self-Check Database</h5>
                    <p class="text-muted">
                        Scan all leads in your CRM for duplicates based on phone number, SSN, or account number. Export results to CSV.
                    </p>
                    
                    <form action="{{ route('admin.dupe-checker.self-check') }}" method="POST" class="mt-4">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Check By:</label>
                            <select name="check_by" class="form-select">
                                <option value="phone">Phone Number</option>
                                <option value="ssn">SSN</option>
                                <option value="account">Account Number</option>
                                <option value="all">All Fields</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-search me-1"></i> Run Self-Check
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- File Comparison -->
        <div class="col-lg-4">
            <div class="card dupe-card h-100">
                <div class="card-body text-center">
                    <div class="feature-icon bg-soft-success text-success mx-auto">
                        <i class="bx bx-git-compare"></i>
                    </div>
                    <h5 class="card-title">File Comparison</h5>
                    <p class="text-muted">
                        Upload two files and compare File 2 against File 1 by phone number. Get a CSV with "Duplicate" or "Unique" status for each record.
                    </p>
                    
                    <form action="{{ route('admin.dupe-checker.file-comparison') }}" method="POST" enctype="multipart/form-data" class="mt-4">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">File 1 (Master Data):</label>
                            <input type="file" name="file1" class="form-control" accept=".xlsx,.xls,.csv" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">File 2 (Check Against):</label>
                            <input type="file" name="file2" class="form-control" accept=".xlsx,.xls,.csv" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bx bx-transfer me-1"></i> Compare Files
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Auto Deduplication -->
        <div class="col-lg-4">
            <div class="card dupe-card h-100">
                <div class="card-body text-center">
                    <div class="feature-icon bg-soft-warning text-warning mx-auto">
                        <i class="bx bx-merge"></i>
                    </div>
                    <h5 class="card-title">Auto-Deduplicate</h5>
                    <p class="text-muted">
                        Automatically find duplicate leads by phone number, merge their data into one complete record, and remove extras.
                    </p>
                    
                    <div class="alert alert-warning text-start mt-4">
                        <i class="bx bx-info-circle me-2"></i>
                        <strong>Warning:</strong> This action will merge duplicates and delete extra records. This cannot be undone.
                    </div>
                    
                    <form action="{{ route('admin.dupe-checker.run-deduplication') }}" method="POST" onsubmit="return confirm('Are you sure you want to run automatic deduplication? This will merge duplicate leads and cannot be undone.');">
                        @csrf
                        <button type="submit" class="btn btn-warning w-100">
                            <i class="bx bx-merge me-1"></i> Run Deduplication
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Instructions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bx bx-book-open me-2"></i>How to Use
                    </h5>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <h6 class="text-primary">Self-Check Database</h6>
                            <ol class="text-muted">
                                <li>Select the field to check for duplicates</li>
                                <li>Click "Run Self-Check"</li>
                                <li>Download the CSV report showing all duplicates</li>
                            </ol>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-success">File Comparison</h6>
                            <ol class="text-muted">
                                <li>Upload File 1 (your master/existing data)</li>
                                <li>Upload File 2 (new data to check)</li>
                                <li>Download CSV with status for each record in File 2</li>
                            </ol>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-warning">Auto-Deduplicate</h6>
                            <ol class="text-muted">
                                <li>Review your data before running</li>
                                <li>Click "Run Deduplication"</li>
                                <li>System merges duplicate records automatically</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/toastr/build/toastr.min.js') }}"></script>
@endsection
