@extends('layouts.master')

@section('title', 'Attendance Report')

@section('css')
<style>.glassmorphism-card{background:rgba(30,41,59,.95);backdrop-filter:blur(20px);border:1px solid rgba(212,175,55,.2);border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,.3)}.page-header{color:#d4af37;font-weight:700;font-size:1.75rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:.75rem}.stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:1.5rem;margin-bottom:2rem}.stat-box{background:rgba(15,23,42,.6);padding:1.5rem;border-radius:12px;border:1px solid rgba(212,175,55,.2);text-align:center}.stat-value{font-size:2rem;font-weight:700;margin-bottom:.5rem}.stat-label{color:#94a3b8;font-size:.875rem;text-transform:uppercase}.stat-present{color:#10b981}.stat-absent{color:#ef4444}.stat-late{color:#eab308}.stat-total{color:#3b82f6}.table-dark-custom{color:#cbd5e1}.table-dark-custom thead th{background:rgba(15,23,42,.8);color:#d4af37;border-color:rgba(212,175,55,.2);font-weight:600}.table-dark-custom tbody td{border-color:rgba(212,175,55,.1)}.badge-present{background:#10b981;color:#fff;padding:.35rem .75rem;border-radius:6px;font-size:.75rem;font-weight:600}.badge-absent{background:#ef4444;color:#fff;padding:.35rem .75rem;border-radius:6px;font-size:.75rem;font-weight:600}.badge-late{background:#eab308;color:#fff;padding:.35rem .75rem;border-radius:6px;font-size:.75rem;font-weight:600}.filter-panel{background:rgba(15,23,42,.6);padding:1.5rem;border-radius:12px;margin-bottom:1.5rem;border:1px solid rgba(212,175,55,.2)}.form-control,.form-select{background:rgba(15,23,42,.8);border:1px solid rgba(212,175,55,.3);color:#cbd5e1;border-radius:8px;padding:.6rem .75rem}.btn-filter{background:linear-gradient(135deg,#3b82f6 0%,#1d4ed8 100%);border:none;color:#fff;font-weight:600;padding:.6rem 1.5rem;border-radius:8px}.btn-export{background:linear-gradient(135deg,#10b981 0%,#059669 100%);border:none;color:#fff;font-weight:600;padding:.75rem 1.5rem;border-radius:8px}</style>
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1')Reports@endslot
@slot('title')Attendance Report@endslot
@endcomponent

<div class="row"><div class="col-12">
<h2 class="page-header"><i class="mdi mdi-calendar-check"></i>Employee Attendance Report</h2>

<div class="filter-panel">
<form class="row g-3 align-items-end">
<div class="col-md-3"><label class="form-label" style="color:#cbd5e1;font-size:.875rem">Employee</label><select class="form-select"><option value="">All Employees</option><option>John Smith</option><option>Sarah Johnson</option></select></div>
<div class="col-md-3"><label class="form-label" style="color:#cbd5e1;font-size:.875rem">From Date</label><input type="date" class="form-control"></div>
<div class="col-md-3"><label class="form-label" style="color:#cbd5e1;font-size:.875rem">To Date</label><input type="date" class="form-control"></div>
<div class="col-md-3"><button type="submit" class="btn btn-filter w-100"><i class="mdi mdi-filter me-2"></i>Filter</button></div>
</form>
</div>

<div class="stats-row">
<div class="stat-box"><div class="stat-value stat-present">85%</div><div class="stat-label">Attendance Rate</div></div>
<div class="stat-box"><div class="stat-value stat-present">238</div><div class="stat-label">Present Days</div></div>
<div class="stat-box"><div class="stat-value stat-absent">42</div><div class="stat-label">Absent Days</div></div>
<div class="stat-box"><div class="stat-value stat-late">18</div><div class="stat-label">Late Arrivals</div></div>
</div>

<div class="glassmorphism-card mb-4"><div class="card-body">
<div class="d-flex justify-content-between align-items-center mb-4">
<h5 style="color:#d4af37;font-size:1.25rem;font-weight:600;margin:0"><i class="mdi mdi-table me-2"></i>Attendance Grid</h5>
<button class="btn-export"><i class="mdi mdi-microsoft-excel me-2"></i>Export to Excel</button>
</div>

<div class="table-responsive"><table class="table table-dark-custom table-bordered">
<thead><tr><th>Employee</th><th>Present</th><th>Absent</th><th>Late</th><th>Attendance Rate</th><th>Actions</th></tr></thead>
<tbody>
<tr><td>John Smith</td><td style="color:#10b981;font-weight:600">22</td><td style="color:#ef4444">2</td><td style="color:#eab308">1</td><td><span class="badge-present">88%</span></td><td><button class="btn btn-sm" style="background:rgba(59,130,246,.2);color:#60a5fa"><i class="mdi mdi-eye"></i></button></td></tr>
<tr><td>Sarah Johnson</td><td style="color:#10b981;font-weight:600">24</td><td style="color:#ef4444">0</td><td style="color:#eab308">1</td><td><span class="badge-present">96%</span></td><td><button class="btn btn-sm" style="background:rgba(59,130,246,.2);color:#60a5fa"><i class="mdi mdi-eye"></i></button></td></tr>
<tr><td>Michael Brown</td><td style="color:#10b981;font-weight:600">20</td><td style="color:#ef4444">4</td><td style="color:#eab308">1</td><td><span class="badge-late">80%</span></td><td><button class="btn btn-sm" style="background:rgba(59,130,246,.2);color:#60a5fa"><i class="mdi mdi-eye"></i></button></td></tr>
<tr><td>Emily Davis</td><td style="color:#10b981;font-weight:600">23</td><td style="color:#ef4444">1</td><td style="color:#eab308">1</td><td><span class="badge-present">92%</span></td><td><button class="btn btn-sm" style="background:rgba(59,130,246,.2);color:#60a5fa"><i class="mdi mdi-eye"></i></button></td></tr>
</tbody>
</table></div>
</div></div>

</div></div>
@endsection
