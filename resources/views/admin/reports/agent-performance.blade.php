@extends('layouts.master')

@section('title')
    Agent Performance
@endsection

@section('css')
    <style>
        .glassmorphism-card{background:rgba(30,41,59,.95);backdrop-filter:blur(20px);border:1px solid rgba(212,175,55,.2);border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,.3)}.page-header{color:#d4af37;font-weight:700;font-size:1.75rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:.75rem}.leaderboard-item{background:rgba(15,23,42,.6);padding:1.5rem;border-radius:12px;border:1px solid rgba(212,175,55,.2);margin-bottom:1rem;display:flex;align-items:center;gap:1.5rem;transition:all .3s ease}.leaderboard-item:hover{transform:translateX(8px);border-color:rgba(212,175,55,.4)}.rank-badge{width:60px;height:60px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700;background:linear-gradient(135deg,#d4af37 0%,#b8941f 100%);color:#0f172a}.rank-1{background:linear-gradient(135deg,#ffd700 0%,#ffed4e 100%)}.rank-2{background:linear-gradient(135deg,#c0c0c0 0%,#e8e8e8 100%)}.rank-3{background:linear-gradient(135deg,#cd7f32 0%,#e8a87c 100%)}.agent-info{flex:1}.agent-name{color:#d4af37;font-size:1.25rem;font-weight:600;margin-bottom:.5rem}.agent-stats{display:flex;gap:2rem;color:#cbd5e1;font-size:.875rem}.stat-item{display:flex;flex-direction:column}.stat-value{font-size:1.5rem;font-weight:700;color:#10b981}.performance-badge{padding:.5rem 1rem;border-radius:8px;font-size:.875rem;font-weight:600;color:#fff}.badge-top{background:linear-gradient(135deg,#10b981 0%,#059669 100%)}.badge-rising{background:linear-gradient(135deg,#3b82f6 0%,#1d4ed8 100%)}.badge-needs{background:linear-gradient(135deg,#eab308 0%,#ca8a04 100%)}.table-dark-custom{color:#cbd5e1}.table-dark-custom thead th{background:rgba(15,23,42,.8);color:#d4af37;border-color:rgba(212,175,55,.2);font-weight:600}.table-dark-custom tbody td{border-color:rgba(212,175,55,.1)}.table-dark-custom tbody tr:hover{background:rgba(212,175,55,.05)}.section-title{color:#d4af37;font-size:1.25rem;font-weight:600;margin-bottom:1.5rem;padding-bottom:.75rem;border-bottom:2px solid rgba(212,175,55,.3)}
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Reports
        @endslot
        @slot('title')
            Agent Performance
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <h2 class="page-header">
                <i class="mdi mdi-trophy"></i>
                Agent Performance Leaderboard
            </h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="glassmorphism-card mb-4">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="mdi mdi-chart-bar me-2"></i>
                        Top Performers
                    </h5>

                    <div class="leaderboard-item">
                        <div class="rank-badge rank-1">1</div>
                        <div class="agent-info">
                            <div class="agent-name">Sarah Johnson <span class="performance-badge badge-top">Top Performer</span></div>
                            <div class="agent-stats">
                                <div class="stat-item">
                                    <span class="stat-value">156</span>
                                    <span style="color:#94a3b8">Total Leads</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value">104</span>
                                    <span style="color:#94a3b8">Conversions</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value">66.7%</span>
                                    <span style="color:#94a3b8">Rate</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value">$89K</span>
                                    <span style="color:#94a3b8">Revenue</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="leaderboard-item">
                        <div class="rank-badge rank-2">2</div>
                        <div class="agent-info">
                            <div class="agent-name">John Smith <span class="performance-badge badge-rising">Rising Star</span></div>
                            <div class="agent-stats">
                                <div class="stat-item">
                                    <span class="stat-value">142</span>
                                    <span style="color:#94a3b8">Total Leads</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value">90</span>
                                    <span style="color:#94a3b8">Conversions</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value">63.4%</span>
                                    <span style="color:#94a3b8">Rate</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value">$76K</span>
                                    <span style="color:#94a3b8">Revenue</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="leaderboard-item">
                        <div class="rank-badge rank-3">3</div>
                        <div class="agent-info">
                            <div class="agent-name">Michael Brown</div>
                            <div class="agent-stats">
                                <div class="stat-item">
                                    <span class="stat-value">128</span>
                                    <span style="color:#94a3b8">Total Leads</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value">76</span>
                                    <span style="color:#94a3b8">Conversions</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value">59.4%</span>
                                    <span style="color:#94a3b8">Rate</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value">$62K</span>
                                    <span style="color:#94a3b8">Revenue</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="leaderboard-item">
                        <div class="rank-badge">4</div>
                        <div class="agent-info">
                            <div class="agent-name">Emily Davis <span class="performance-badge badge-needs">Needs Support</span></div>
                            <div class="agent-stats">
                                <div class="stat-item">
                                    <span class="stat-value">98</span>
                                    <span style="color:#94a3b8">Total Leads</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value">48</span>
                                    <span style="color:#94a3b8">Conversions</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value" style="color:#eab308">49.0%</span>
                                    <span style="color:#94a3b8">Rate</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value">$41K</span>
                                    <span style="color:#94a3b8">Revenue</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="glassmorphism-card">
                <div class="card-body">
                    <h5 class="section-title">
                        <i class="mdi mdi-chart-donut me-2"></i>
                        Performance Metrics
                    </h5>

                    <div style="background:rgba(15,23,42,.4);padding:1.25rem;border-radius:8px;margin-bottom:1rem">
                        <div style="color:#94a3b8;font-size:.875rem;margin-bottom:.5rem">Average Conversion Rate</div>
                        <div style="font-size:2rem;font-weight:700;color:#10b981">59.6%</div>
                    </div>

                    <div style="background:rgba(15,23,42,.4);padding:1.25rem;border-radius:8px;margin-bottom:1rem">
                        <div style="color:#94a3b8;font-size:.875rem;margin-bottom:.5rem">Total Team Revenue</div>
                        <div style="font-size:2rem;font-weight:700;color:#d4af37">$268K</div>
                    </div>

                    <div style="background:rgba(15,23,42,.4);padding:1.25rem;border-radius:8px">
                        <div style="color:#94a3b8;font-size:.875rem;margin-bottom:.5rem">Active Agents</div>
                        <div style="font-size:2rem;font-weight:700;color:#3b82f6">12</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
