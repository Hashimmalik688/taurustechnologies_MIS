@extends('layouts.master')

@section('title')
    Sales Analytics
@endsection

@section('css')
    <style>
        .glassmorphism-card {
            background: rgba(30, 41, 59, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .page-header {
            color: #d4af37;
            font-weight: 700;
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .period-selector {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
        }

        .period-btn {
            padding: 0.6rem 1.2rem;
            border: 1px solid rgba(212, 175, 55, 0.3);
            background: rgba(15, 23, 42, 0.6);
            color: #cbd5e1;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .period-btn.active {
            background: linear-gradient(135deg, #d4af37 0%, #b8941f 100%);
            color: #0f172a;
            border-color: #d4af37;
        }

        .period-btn:hover {
            border-color: #d4af37;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(15, 23, 42, 0.6);
            padding: 1.5rem;
            border-radius: 12px;
            border: 1px solid rgba(212, 175, 55, 0.2);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            border-color: rgba(212, 175, 55, 0.4);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #d4af37;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #94a3b8;
            font-size: 0.875rem;
            text-transform: uppercase;
        }

        .status-breakdown-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }

        .status-card {
            background: rgba(15, 23, 42, 0.4);
            padding: 1.25rem;
            border-radius: 8px;
            border-left: 4px solid;
            text-align: center;
        }

        .status-pending { border-left-color: #eab308; }
        .status-approved { border-left-color: #10b981; }
        .status-rejected { border-left-color: #ef4444; }
        .status-forwarded { border-left-color: #3b82f6; }

        .status-count {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .status-label {
            color: #94a3b8;
            font-size: 0.875rem;
        }

        .table-dark-custom {
            color: #cbd5e1;
        }

        .table-dark-custom thead th {
            background: rgba(15, 23, 42, 0.8);
            color: #d4af37;
            border-color: rgba(212, 175, 55, 0.2);
            font-weight: 600;
        }

        .table-dark-custom tbody td {
            border-color: rgba(212, 175, 55, 0.1);
        }

        .table-dark-custom tbody tr:hover {
            background: rgba(212, 175, 55, 0.05);
        }

        .section-title {
            color: #d4af37;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid rgba(212, 175, 55, 0.3);
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Reports
        @endslot
        @slot('title')
            Sales Analytics
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <h2 class="page-header">
                <i class="mdi mdi-chart-line"></i>
                Sales Analytics Dashboard
            </h2>

            <div class="period-selector">
                <button class="period-btn">Today</button>
                <button class="period-btn">This Week</button>
                <button class="period-btn active">This Month</button>
                <button class="period-btn">This Year</button>
                <button class="period-btn">Custom Range</button>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">248</div>
                    <div class="stat-label">Total Leads</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">156</div>
                    <div class="stat-label">Converted</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">62.9%</div>
                    <div class="stat-label">Conversion Rate</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">$245K</div>
                    <div class="stat-label">Total Revenue</div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12">
                    <div class="glassmorphism-card">
                        <div class="card-body">
                            <h5 class="section-title">
                                <i class="mdi mdi-chart-pie me-2"></i>
                                Status Breakdown
                            </h5>
                            <div class="status-breakdown-grid">
                                <div class="status-card status-pending">
                                    <div class="status-count" style="color: #eab308;">42</div>
                                    <div class="status-label">Pending</div>
                                </div>
                                <div class="status-card status-approved">
                                    <div class="status-count" style="color: #10b981;">156</div>
                                    <div class="status-label">Approved</div>
                                </div>
                                <div class="status-card status-rejected">
                                    <div class="status-count" style="color: #ef4444;">28</div>
                                    <div class="status-label">Rejected</div>
                                </div>
                                <div class="status-card status-forwarded">
                                    <div class="status-count" style="color: #3b82f6;">22</div>
                                    <div class="status-label">Forwarded</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="glassmorphism-card">
                        <div class="card-body">
                            <h5 class="section-title">
                                <i class="mdi mdi-account-tie me-2"></i>
                                Sales by Agent
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-dark-custom table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Agent Name</th>
                                            <th>Total Leads</th>
                                            <th>Converted</th>
                                            <th>Conversion Rate</th>
                                            <th>Total Revenue</th>
                                            <th>Avg. Deal Size</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>John Smith</td>
                                            <td>85</td>
                                            <td>54</td>
                                            <td style="color: #10b981; font-weight: 600;">63.5%</td>
                                            <td style="color: #d4af37; font-weight: 600;">$89,450</td>
                                            <td>$1,656</td>
                                        </tr>
                                        <tr>
                                            <td>Sarah Johnson</td>
                                            <td>72</td>
                                            <td>48</td>
                                            <td style="color: #10b981; font-weight: 600;">66.7%</td>
                                            <td style="color: #d4af37; font-weight: 600;">$76,230</td>
                                            <td>$1,588</td>
                                        </tr>
                                        <tr>
                                            <td>Michael Brown</td>
                                            <td>56</td>
                                            <td>32</td>
                                            <td style="color: #eab308; font-weight: 600;">57.1%</td>
                                            <td style="color: #d4af37; font-weight: 600;">$48,920</td>
                                            <td>$1,529</td>
                                        </tr>
                                        <tr>
                                            <td>Emily Davis</td>
                                            <td>35</td>
                                            <td>22</td>
                                            <td style="color: #10b981; font-weight: 600;">62.9%</td>
                                            <td style="color: #d4af37; font-weight: 600;">$30,400</td>
                                            <td>$1,382</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
