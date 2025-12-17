@extends('layouts.master')

@section('title', 'Revenue Tracking')

@section('css')
<style>.glassmorphism-card{background:rgba(30,41,59,.95);backdrop-filter:blur(20px);border:1px solid rgba(212,175,55,.2);border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,.3)}.page-header{color:#d4af37;font-weight:700;font-size:1.75rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:.75rem}.revenue-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem;margin-bottom:2rem}.revenue-card{background:rgba(15,23,42,.6);padding:1.75rem;border-radius:12px;border:1px solid rgba(212,175,55,.2);text-align:center}.revenue-value{font-size:2.5rem;font-weight:700;margin-bottom:.5rem}.revenue-label{color:#94a3b8;font-size:.875rem;text-transform:uppercase}.stat-income{color:#10b981}.stat-expense{color:#ef4444}.stat-profit{color:#d4af37}.section-title{color:#d4af37;font-size:1.25rem;font-weight:600;margin-bottom:1.5rem;padding-bottom:.75rem;border-bottom:2px solid rgba(212,175,55,.3)}.month-item{background:rgba(15,23,42,.4);padding:1.25rem;border-radius:8px;margin-bottom:1rem;display:flex;justify-content:space-between;align-items:center;border-left:4px solid #d4af37}.month-name{color:#cbd5e1;font-weight:600;font-size:1.1rem}.month-stats{display:flex;gap:2rem}.month-value{text-align:center}.btn-export{background:linear-gradient(135deg,#10b981 0%,#059669 100%);border:none;color:#fff;font-weight:600;padding:.75rem 1.5rem;border-radius:8px;transition:all .3s ease}.btn-export:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(16,185,129,.4)}</style>
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1')Reports@endslot
@slot('title')Revenue Tracking@endslot
@endcomponent

<div class="row"><div class="col-12">
<h2 class="page-header"><i class="mdi mdi-cash-multiple"></i>Revenue Tracking & Analysis</h2>

<div class="revenue-grid">
<div class="revenue-card"><div class="revenue-value stat-income">$524,230</div><div class="revenue-label">Total Income</div></div>
<div class="revenue-card"><div class="revenue-value stat-expense">$186,450</div><div class="revenue-label">Total Expenses</div></div>
<div class="revenue-card"><div class="revenue-value stat-profit">$337,780</div><div class="revenue-label">Net Profit</div></div>
</div>

<div class="row">
<div class="col-lg-8">
<div class="glassmorphism-card mb-4"><div class="card-body">
<div class="d-flex justify-content-between align-items-center mb-4">
<h5 class="section-title mb-0"><i class="mdi mdi-calendar-month me-2"></i>Monthly Revenue Trend</h5>
<button class="btn-export"><i class="mdi mdi-microsoft-excel me-2"></i>Export to Excel</button>
</div>

<div class="month-item"><div class="month-name">September 2025</div><div class="month-stats">
<div class="month-value"><div style="color:#10b981;font-weight:700;font-size:1.25rem">$58,450</div><small style="color:#94a3b8">Income</small></div>
<div class="month-value"><div style="color:#ef4444;font-weight:700;font-size:1.25rem">$21,230</div><small style="color:#94a3b8">Expense</small></div>
<div class="month-value"><div style="color:#d4af37;font-weight:700;font-size:1.25rem">$37,220</div><small style="color:#94a3b8">Profit</small></div>
</div></div>

<div class="month-item"><div class="month-name">August 2025</div><div class="month-stats">
<div class="month-value"><div style="color:#10b981;font-weight:700;font-size:1.25rem">$52,890</div><small style="color:#94a3b8">Income</small></div>
<div class="month-value"><div style="color:#ef4444;font-weight:700;font-size:1.25rem">$18,900</div><small style="color:#94a3b8">Expense</small></div>
<div class="month-value"><div style="color:#d4af37;font-weight:700;font-size:1.25rem">$33,990</div><small style="color:#94a3b8">Profit</small></div>
</div></div>

<div class="month-item"><div class="month-name">July 2025</div><div class="month-stats">
<div class="month-value"><div style="color:#10b981;font-weight:700;font-size:1.25rem">$48,120</div><small style="color:#94a3b8">Income</small></div>
<div class="month-value"><div style="color:#ef4444;font-weight:700;font-size:1.25rem">$17,450</div><small style="color:#94a3b8">Expense</small></div>
<div class="month-value"><div style="color:#d4af37;font-weight:700;font-size:1.25rem">$30,670</div><small style="color:#94a3b8">Profit</small></div>
</div></div>
</div></div>
</div>

<div class="col-lg-4">
<div class="glassmorphism-card"><div class="card-body">
<h5 class="section-title"><i class="mdi mdi-trending-up me-2"></i>Projected Revenue</h5>
<div style="background:rgba(15,23,42,.6);padding:2rem;border-radius:12px;text-align:center;border:1px solid rgba(212,175,55,.2)">
<div style="color:#94a3b8;font-size:.875rem;margin-bottom:1rem">Q4 2025 Projection</div>
<div style="font-size:3rem;font-weight:700;color:#d4af37;margin-bottom:1rem">$186K</div>
<div style="color:#10b981;font-size:1rem;font-weight:600"><i class="mdi mdi-arrow-up"></i> +12.5% from Q3</div>
</div>
</div></div>
</div>
</div>

</div></div>
@endsection
