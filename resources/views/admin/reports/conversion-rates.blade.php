@extends('layouts.master')

@section('title', 'Conversion Rates')

@section('css')
<style>.glassmorphism-card{background:rgba(30,41,59,.95);backdrop-filter:blur(20px);border:1px solid rgba(212,175,55,.2);border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,.3)}.page-header{color:#d4af37;font-weight:700;font-size:1.75rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:.75rem}.conversion-gauge{width:200px;height:200px;margin:0 auto 2rem;position:relative;display:flex;align-items:center;justify-content:center;background:conic-gradient(#10b981 0% 63%,rgba(212,175,55,.2) 63% 100%);border-radius:50%;box-shadow:0 8px 24px rgba(0,0,0,.4)}.gauge-inner{width:150px;height:150px;background:#1e293b;border-radius:50%;display:flex;flex-direction:column;align-items:center;justify-content:center}.gauge-value{font-size:3rem;font-weight:700;color:#10b981}.gauge-label{color:#94a3b8;font-size:.875rem}.section-title{color:#d4af37;font-size:1.25rem;font-weight:600;margin-bottom:1.5rem;padding-bottom:.75rem;border-bottom:2px solid rgba(212,175,55,.3)}.source-item{background:rgba(15,23,42,.4);padding:1.25rem;border-radius:8px;margin-bottom:1rem;display:flex;justify-content:space-between;align-items:center}.source-name{color:#cbd5e1;font-weight:600}.source-rate{font-size:1.5rem;font-weight:700;color:#10b981}.progress-bar-custom{height:10px;background:rgba(212,175,55,.2);border-radius:5px;overflow:hidden;margin-top:.5rem}.progress-fill{height:100%;background:linear-gradient(90deg,#10b981 0%,#059669 100%);transition:width .3s ease}.table-dark-custom{color:#cbd5e1}.table-dark-custom thead th{background:rgba(15,23,42,.8);color:#d4af37;border-color:rgba(212,175,55,.2);font-weight:600}.table-dark-custom tbody td{border-color:rgba(212,175,55,.1)}</style>
@endsection

@section('content')
@component('components.breadcrumb')
@slot('li_1')Reports@endslot
@slot('title')Conversion Rates@endslot
@endcomponent

<div class="row"><div class="col-12">
<h2 class="page-header"><i class="mdi mdi-percent"></i>Conversion Rate Analytics</h2>
</div></div>

<div class="row">
<div class="col-lg-4">
<div class="glassmorphism-card mb-4"><div class="card-body text-center">
<h5 class="section-title">Overall Conversion Rate</h5>
<div class="conversion-gauge"><div class="gauge-inner"><div class="gauge-value">62.9%</div><div class="gauge-label">Success Rate</div></div></div>
<p style="color:#94a3b8">Based on 248 total leads</p>
</div></div>
</div>

<div class="col-lg-8">
<div class="glassmorphism-card mb-4"><div class="card-body">
<h5 class="section-title"><i class="mdi mdi-source-branch me-2"></i>Conversion by Source</h5>

<div class="source-item"><div><div class="source-name">Referral</div><div class="progress-bar-custom"><div class="progress-fill" style="width:72%"></div></div></div><div class="source-rate">72%</div></div>

<div class="source-item"><div><div class="source-name">Direct Contact</div><div class="progress-bar-custom"><div class="progress-fill" style="width:68%"></div></div></div><div class="source-rate">68%</div></div>

<div class="source-item"><div><div class="source-name">Website</div><div class="progress-bar-custom"><div class="progress-fill" style="width:58%"></div></div></div><div class="source-rate">58%</div></div>

<div class="source-item"><div><div class="source-name">Social Media</div><div class="progress-bar-custom"><div class="progress-fill" style="width:54%"></div></div></div><div class="source-rate">54%</div></div>

<div class="source-item"><div><div class="source-name">Email Campaign</div><div class="progress-bar-custom"><div class="progress-fill" style="width:48%"></div></div></div><div class="source-rate">48%</div></div>
</div></div>
</div>
</div>

<div class="row">
<div class="col-12">
<div class="glassmorphism-card"><div class="card-body">
<h5 class="section-title"><i class="mdi mdi-account-tie me-2"></i>Conversion by Agent</h5>
<div class="table-responsive"><table class="table table-dark-custom table-bordered">
<thead><tr><th>Agent</th><th>Total Leads</th><th>Converted</th><th>Rate</th><th>Trend</th></tr></thead>
<tbody>
<tr><td>Sarah Johnson</td><td>156</td><td>104</td><td style="color:#10b981;font-weight:700">66.7%</td><td style="color:#10b981"><i class="mdi mdi-trending-up"></i> +5.2%</td></tr>
<tr><td>John Smith</td><td>142</td><td>90</td><td style="color:#10b981;font-weight:700">63.4%</td><td style="color:#10b981"><i class="mdi mdi-trending-up"></i> +3.8%</td></tr>
<tr><td>Michael Brown</td><td>128</td><td>76</td><td style="color:#eab308;font-weight:700">59.4%</td><td style="color:#ef4444"><i class="mdi mdi-trending-down"></i> -2.1%</td></tr>
<tr><td>Emily Davis</td><td>98</td><td>48</td><td style="color:#eab308;font-weight:700">49.0%</td><td style="color:#64748b"><i class="mdi mdi-minus"></i> 0%</td></tr>
</tbody>
</table></div>
</div></div>
</div>
</div>
@endsection
