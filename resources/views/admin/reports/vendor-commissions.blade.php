@extends('layouts.master')

@section('title', 'Vendor Commissions')

@section('css')
<style>.glassmorphism-card{background:rgba(30,41,59,.95);backdrop-filter:blur(20px);border:1px solid rgba(212,175,55,.2);border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,.3)}.page-header{color:#d4af37;font-weight:700;font-size:1.75rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:.75rem}.summary-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem;margin-bottom:2rem}.summary-card{background:rgba(15,23,42,.6);padding:1.75rem;border-radius:12px;border:1px solid rgba(212,175,55,.2);text-align:center}.summary-value{font-size:2.5rem;font-weight:700;margin-bottom:.5rem}.summary-label{color:#94a3b8;font-size:.875rem;text-transform:uppercase}.stat-total{color:#d4af37}.stat-pending{color:#eab308}.stat-paid{color:#10b981}.table-dark-custom{color:#cbd5e1}.table-dark-custom thead th{background:rgba(15,23,42,.8);color:#d4af37;border-color:rgba(212,175,55,.2);font-weight:600}.table-dark-custom tbody td{border-color:rgba(212,175,55,.1)}.table-dark-custom tbody tr:hover{background:rgba(212,175,55,.05)}.badge-pending{background:linear-gradient(135deg,#eab308 0%,#ca8a04 100%);color:#fff;padding:.35rem .75rem;border-radius:6px;font-size:.75rem;font-weight:600}.badge-paid{background:linear-gradient(135deg,#10b981 0%,#059669 100%);color:#fff;padding:.35rem .75rem;border-radius:6px;font-size:.75rem;font-weight:600}.section-title{color:#d4af37;font-size:1.25rem;font-weight:600;margin-bottom:1.5rem;padding-bottom:.75rem;border-bottom:2px solid rgba(212,175,55,.3)}.btn-export{background:linear-gradient(135deg,#10b981 0%,#059669 100%);border:none;color:#fff;font-weight:600;padding:.75rem 1.5rem;border-radius:8px}</style>
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1')Reports@endslot
@slot('title')Vendor Commissions@endslot
@endcomponent

<div class="row"><div class="col-12">
<h2 class="page-header"><i class="mdi mdi-cash-check"></i>Vendor Commission Report</h2>

<div class="summary-grid">
<div class="summary-card"><div class="summary-value stat-total">$124,560</div><div class="summary-label">Total Commissions</div></div>
<div class="summary-card"><div class="summary-value stat-pending">$42,230</div><div class="summary-label">Pending Payments</div></div>
<div class="summary-card"><div class="summary-value stat-paid">$82,330</div><div class="summary-label">Paid This Month</div></div>
</div>

<div class="glassmorphism-card mb-4"><div class="card-body">
<div class="d-flex justify-content-between align-items-center mb-4">
<h5 class="section-title mb-0"><i class="mdi mdi-table me-2"></i>Vendor Payment Summary</h5>
<button class="btn-export"><i class="mdi mdi-microsoft-excel me-2"></i>Export Report</button>
</div>

<div class="table-responsive"><table class="table table-dark-custom table-bordered">
<thead><tr><th>Vendor Name</th><th>Company</th><th>Type</th><th>Total Earned</th><th>Paid</th><th>Pending</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
<tr><td>John Smith</td><td>Smith Insurance Co.</td><td>US Agent</td><td style="color:#d4af37;font-weight:600">$45,230</td><td style="color:#10b981">$32,780</td><td style="color:#eab308">$12,450</td><td><span class="badge-pending">Pending</span></td><td><button class="btn btn-sm" style="background:rgba(59,130,246,.2);color:#60a5fa">Pay Now</button></td></tr>
<tr><td>Sarah Johnson</td><td>Johnson & Associates</td><td>Vendor</td><td style="color:#d4af37;font-weight:600">$38,120</td><td style="color:#10b981">$29,890</td><td style="color:#eab308">$8,230</td><td><span class="badge-pending">Pending</span></td><td><button class="btn btn-sm" style="background:rgba(59,130,246,.2);color:#60a5fa">Pay Now</button></td></tr>
<tr><td>Michael Brown</td><td>Brown Supplies Ltd.</td><td>Supplier</td><td style="color:#d4af37;font-weight:600">$24,890</td><td style="color:#10b981">$24,890</td><td style="color:#94a3b8">$0</td><td><span class="badge-paid">Paid</span></td><td><button class="btn btn-sm" style="background:rgba(100,116,139,.3);color:#cbd5e1">View</button></td></tr>
<tr><td>Emily Davis</td><td>Davis Solutions</td><td>Vendor</td><td style="color:#d4af37;font-weight:600">$16,320</td><td style="color:#10b981">$16,320</td><td style="color:#94a3b8">$0</td><td><span class="badge-paid">Paid</span></td><td><button class="btn btn-sm" style="background:rgba(100,116,139,.3);color:#cbd5e1">View</button></td></tr>
</tbody>
</table></div>
</div></div>

<div class="row">
<div class="col-12">
<div class="glassmorphism-card"><div class="card-body">
<h5 class="section-title"><i class="mdi mdi-history me-2"></i>Recent Payment History</h5>
<div class="table-responsive"><table class="table table-dark-custom table-bordered">
<thead><tr><th>Date</th><th>Vendor</th><th>Amount</th><th>Method</th><th>Reference</th><th>Status</th></tr></thead>
<tbody>
<tr><td>2025-09-28</td><td>Michael Brown</td><td style="color:#10b981;font-weight:600">$8,450</td><td>Bank Transfer</td><td>PAY-001234</td><td><span class="badge-paid">Completed</span></td></tr>
<tr><td>2025-09-25</td><td>Emily Davis</td><td style="color:#10b981;font-weight:600">$6,230</td><td>Check</td><td>CHK-005678</td><td><span class="badge-paid">Completed</span></td></tr>
<tr><td>2025-09-20</td><td>John Smith</td><td style="color:#10b981;font-weight:600">$12,100</td><td>Bank Transfer</td><td>PAY-001200</td><td><span class="badge-paid">Completed</span></td></tr>
</tbody>
</table></div>
</div></div>
</div>
</div>

</div></div>
@endsection
