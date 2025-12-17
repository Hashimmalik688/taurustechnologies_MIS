@extends('layouts.master')

@section('title', 'Custom Report')

@section('css')
<style>.glassmorphism-card{background:rgba(30,41,59,.95);backdrop-filter:blur(20px);border:1px solid rgba(212,175,55,.2);border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,.3)}.page-header{color:#d4af37;font-weight:700;font-size:1.75rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:.75rem}.report-builder{background:rgba(15,23,42,.6);padding:2rem;border-radius:12px;border:1px solid rgba(212,175,55,.2);margin-bottom:2rem}.section-header{color:#d4af37;font-size:1.1rem;font-weight:600;margin-bottom:1.5rem;padding-bottom:.75rem;border-bottom:2px solid rgba(212,175,55,.3)}.form-label{color:#cbd5e1;font-weight:500;margin-bottom:.5rem;font-size:.875rem}.form-control,.form-select{background:rgba(15,23,42,.8);border:1px solid rgba(212,175,55,.3);color:#cbd5e1;border-radius:8px;padding:.75rem}.form-control:focus,.form-select:focus{background:rgba(15,23,42,.95);border-color:#d4af37;color:#cbd5e1;box-shadow:0 0 0 .2rem rgba(212,175,55,.25)}.form-select option{background:#0f172a;color:#cbd5e1}.checkbox-group{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-top:1rem}.checkbox-item{background:rgba(15,23,42,.4);padding:1rem;border-radius:8px;border:1px solid rgba(212,175,55,.1);display:flex;align-items:center;gap:.75rem;cursor:pointer;transition:all .3s ease}.checkbox-item:hover{border-color:rgba(212,175,55,.4);background:rgba(212,175,55,.05)}.checkbox-item input[type="checkbox"]{width:20px;height:20px;cursor:pointer}.checkbox-item label{cursor:pointer;color:#cbd5e1;margin:0}.gold-gradient-btn{background:linear-gradient(135deg,#d4af37 0%,#b8941f 100%);border:none;color:#0f172a;font-weight:600;padding:.75rem 2rem;border-radius:8px;transition:all .3s ease;box-shadow:0 4px 12px rgba(212,175,55,.3)}.gold-gradient-btn:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(212,175,55,.5);color:#0f172a}.btn-reset{background:rgba(100,116,139,.3);border:1px solid rgba(100,116,139,.5);color:#cbd5e1;font-weight:500;padding:.75rem 2rem;border-radius:8px}.results-placeholder{text-align:center;padding:4rem 2rem;color:#94a3b8}.table-dark-custom{color:#cbd5e1}.table-dark-custom thead th{background:rgba(15,23,42,.8);color:#d4af37;border-color:rgba(212,175,55,.2);font-weight:600}.table-dark-custom tbody td{border-color:rgba(212,175,55,.1)}</style>
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1')Reports@endslot
@slot('title')Custom Report@endslot
@endcomponent

<div class="row"><div class="col-12">
<h2 class="page-header"><i class="mdi mdi-file-chart"></i>Custom Report Builder</h2>

<div class="report-builder">
<form action="{{ route('reports.custom.generate') }}" method="POST">
@csrf
<div class="row">
<div class="col-md-6"><div class="mb-4">
<h5 class="section-header"><i class="mdi mdi-filter me-2"></i>Report Filters</h5>
<div class="mb-3"><label class="form-label">Report Type</label><select class="form-select" name="report_type">
<option value="">Select Report Type</option>
<option value="leads">Leads Report</option>
<option value="vendors">Vendors Report</option>
<option value="ledger">Ledger Report</option>
<option value="attendance">Attendance Report</option>
<option value="sales">Sales Report</option>
</select></div>
<div class="mb-3"><label class="form-label">Date Range</label><div class="row"><div class="col-6"><input type="date" class="form-control" name="date_from" placeholder="From"></div><div class="col-6"><input type="date" class="form-control" name="date_to" placeholder="To"></div></div></div>
<div class="mb-3"><label class="form-label">Status Filter</label><select class="form-select" name="status">
<option value="">All Statuses</option>
<option value="pending">Pending</option>
<option value="approved">Approved</option>
<option value="rejected">Rejected</option>
<option value="active">Active</option>
</select></div>
<div class="mb-3"><label class="form-label">Agent / Employee</label><select class="form-select" name="agent_id">
<option value="">All Agents</option>
<option value="1">John Smith</option>
<option value="2">Sarah Johnson</option>
<option value="3">Michael Brown</option>
</select></div>
</div></div>

<div class="col-md-6"><div class="mb-4">
<h5 class="section-header"><i class="mdi mdi-table-column me-2"></i>Fields to Include</h5>
<div class="checkbox-group">
<div class="checkbox-item"><input type="checkbox" id="field_id" name="fields[]" value="id" checked><label for="field_id">ID</label></div>
<div class="checkbox-item"><input type="checkbox" id="field_name" name="fields[]" value="name" checked><label for="field_name">Name</label></div>
<div class="checkbox-item"><input type="checkbox" id="field_email" name="fields[]" value="email" checked><label for="field_email">Email</label></div>
<div class="checkbox-item"><input type="checkbox" id="field_phone" name="fields[]" value="phone"><label for="field_phone">Phone</label></div>
<div class="checkbox-item"><input type="checkbox" id="field_status" name="fields[]" value="status" checked><label for="field_status">Status</label></div>
<div class="checkbox-item"><input type="checkbox" id="field_date" name="fields[]" value="date" checked><label for="field_date">Date</label></div>
<div class="checkbox-item"><input type="checkbox" id="field_amount" name="fields[]" value="amount"><label for="field_amount">Amount</label></div>
<div class="checkbox-item"><input type="checkbox" id="field_agent" name="fields[]" value="agent"><label for="field_agent">Agent</label></div>
<div class="checkbox-item"><input type="checkbox" id="field_notes" name="fields[]" value="notes"><label for="field_notes">Notes</label></div>
</div>
</div>

<div class="mb-4">
<h5 class="section-header"><i class="mdi mdi-download me-2"></i>Export Format</h5>
<div class="d-flex gap-3">
<div class="checkbox-item" style="flex:1"><input type="radio" name="export_format" value="csv" id="format_csv" checked><label for="format_csv">CSV</label></div>
<div class="checkbox-item" style="flex:1"><input type="radio" name="export_format" value="pdf" id="format_pdf"><label for="format_pdf">PDF</label></div>
<div class="checkbox-item" style="flex:1"><input type="radio" name="export_format" value="excel" id="format_excel"><label for="format_excel">Excel</label></div>
<div class="checkbox-item" style="flex:1"><input type="radio" name="export_format" value="preview" id="format_preview"><label for="format_preview">Preview</label></div>
</div>
</div></div>
</div>

<div class="d-flex justify-content-end gap-3">
<button type="reset" class="btn-reset"><i class="mdi mdi-refresh me-2"></i>Reset</button>
<button type="submit" class="gold-gradient-btn"><i class="mdi mdi-chart-box me-2"></i>Generate Report</button>
</div>
</form>
</div>

<div class="glassmorphism-card" id="resultsSection" style="display:none"><div class="card-body">
<h5 style="color:#d4af37;font-size:1.25rem;font-weight:600;margin-bottom:1.5rem;padding-bottom:.75rem;border-bottom:2px solid rgba(212,175,55,.3)"><i class="mdi mdi-table me-2"></i>Report Results</h5>
<div class="table-responsive"><table class="table table-dark-custom table-bordered">
<thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Status</th><th>Date</th></tr></thead>
<tbody><tr><td colspan="5" class="text-center" style="color:#94a3b8">No data available. Generate a report to see results.</td></tr></tbody>
</table></div>
</div></div>

<div class="results-placeholder">
<i class="mdi mdi-file-chart-outline" style="font-size:5rem;color:#d4af37;opacity:.5"></i>
<h4 style="color:#cbd5e1;margin-top:1rem">Custom Report Generator</h4>
<p>Select your filters and fields above, then click "Generate Report" to create your custom report.</p>
</div>

</div></div>
@endsection
