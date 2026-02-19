@extends('layouts.master')

@section('title')
    Ledger Summary
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .glassmorphism-card {
            background: rgba(30, 41, 59, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .page-header {
            color: var(--bs-gold);
            font-weight: 700;
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(15, 23, 42, 0.6);
            padding: 1.75rem;
            border-radius: 12px;
            border: 1px solid rgba(212, 175, 55, 0.2);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            border-color: rgba(212, 175, 55, 0.4);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-icon-credit {
            background: linear-gradient(135deg, var(--bs-ui-success) 0%, var(--bs-ui-success-dark) 100%);
            color: var(--bs-white);
        }

        .stat-icon-debit {
            background: linear-gradient(135deg, var(--bs-ui-danger) 0%, var(--bs-ui-danger-dark) 100%);
            color: var(--bs-white);
        }

        .stat-icon-balance {
            background: linear-gradient(135deg, var(--bs-gold) 0%, var(--bs-gold-dark) 100%);
            color: var(--bs-surface-900);
        }

        .stat-icon-count {
            background: linear-gradient(135deg, var(--bs-ui-info) 0%, var(--bs-ui-info-dark) 100%);
            color: var(--bs-white);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-credit { color: var(--bs-ui-success); }
        .stat-debit { color: var(--bs-ui-danger); }
        .stat-balance { color: var(--bs-gold); }
        .stat-count { color: var(--bs-ui-info); }

        .stat-label {
            color: var(--bs-surface-400);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .section-title {
            color: var(--bs-gold);
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid rgba(212, 175, 55, 0.3);
        }

        .category-breakdown {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .category-item {
            background: rgba(15, 23, 42, 0.4);
            padding: 1.25rem;
            border-radius: 8px;
            border: 1px solid rgba(212, 175, 55, 0.1);
        }

        .category-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .category-name {
            color: var(--bs-surface-300);
            font-weight: 600;
        }

        .category-amount {
            color: var(--bs-gold);
            font-weight: 700;
            font-size: 1.25rem;
        }

        .category-bar {
            height: 8px;
            background: rgba(212, 175, 55, 0.2);
            border-radius: 4px;
            overflow: hidden;
        }

        .category-bar-fill {
            height: 100%;
            background: linear-gradient(135deg, var(--bs-gold) 0%, var(--bs-gold-dark) 100%);
            transition: width 0.3s ease;
        }

        .filter-panel {
            background: rgba(15, 23, 42, 0.6);
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(212, 175, 55, 0.2);
        }

        .form-label {
            color: var(--bs-surface-300);
            font-weight: 500;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        .form-control, .form-select {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(212, 175, 55, 0.3);
            color: var(--bs-surface-300);
            border-radius: 8px;
            padding: 0.6rem 0.75rem;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(15, 23, 42, 0.95);
            border-color: var(--bs-gold);
            color: var(--bs-surface-300);
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
        }

        .btn-filter {
            background: linear-gradient(135deg, var(--bs-ui-info) 0%, var(--bs-ui-info-dark) 100%);
            border: none;
            color: var(--bs-white);
            font-weight: 600;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
        }
    </style>
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Ledger
        @endslot
        @slot('title')
            Financial Summary
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <h2 class="page-header">
                <i class="mdi mdi-chart-box"></i>
                Financial Summary & Analytics
            </h2>

            <!-- Date Range Filter -->
            <div class="filter-panel">
                <form class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">From Date</label>
                        <input type="text" class="form-control" id="date_from" placeholder="Select date">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">To Date</label>
                        <input type="text" class="form-control" id="date_to" placeholder="Select date">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Quick Select</label>
                        <select class="form-select" id="quick_select">
                            <option value="">Custom Range</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month" selected>This Month</option>
                            <option value="quarter">This Quarter</option>
                            <option value="year">This Year</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-filter w-100">
                            <i class="mdi mdi-refresh me-2"></i>Update Report
                        </button>
                    </div>
                </form>
            </div>

            <!-- Financial Overview Cards -->
            <div class="dashboard-grid">
                <div class="stat-card">
                    <div class="stat-icon stat-icon-credit">
                        <i class="mdi mdi-arrow-up-bold"></i>
                    </div>
                    <div class="stat-value stat-credit">$45,230.00</div>
                    <div class="stat-label">Total Credits</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon stat-icon-debit">
                        <i class="mdi mdi-arrow-down-bold"></i>
                    </div>
                    <div class="stat-value stat-debit">$28,450.00</div>
                    <div class="stat-label">Total Debits</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon stat-icon-balance">
                        <i class="mdi mdi-cash-multiple"></i>
                    </div>
                    <div class="stat-value stat-balance">$16,780.00</div>
                    <div class="stat-label">Net Balance</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon stat-icon-count">
                        <i class="mdi mdi-file-document-multiple"></i>
                    </div>
                    <div class="stat-value stat-count">248</div>
                    <div class="stat-label">Transactions</div>
                </div>
            </div>

            <!-- Category Breakdown -->
            <div class="row mb-4">
                <div class="col-lg-12">
                    <div class="glassmorphism-card">
                        <div class="card-body">
                            <h5 class="section-title">
                                <i class="mdi mdi-tag-multiple me-2"></i>
                                Breakdown by Category
                            </h5>

                            <div class="category-breakdown">
                                <div class="category-item">
                                    <div class="category-header">
                                        <span class="category-name">Commission</span>
                                        <span class="category-amount">$24,500</span>
                                    </div>
                                    <div class="category-bar">
                                        <div class="category-bar-fill" style="width: 85%;"></div>
                                    </div>
 <small class="text-surface-400 u-fs-075" >54% of total</small>
                                </div>

                                <div class="category-item">
                                    <div class="category-header">
                                        <span class="category-name">Payment</span>
                                        <span class="category-amount">$12,350</span>
                                    </div>
                                    <div class="category-bar">
                                        <div class="category-bar-fill" style="width: 65%;"></div>
                                    </div>
 <small class="text-surface-400 u-fs-075" >27% of total</small>
                                </div>

                                <div class="category-item">
                                    <div class="category-header">
                                        <span class="category-name">Expense</span>
                                        <span class="category-amount">$5,230</span>
                                    </div>
                                    <div class="category-bar">
                                        <div class="category-bar-fill" style="width: 35%;"></div>
                                    </div>
 <small class="text-surface-400 u-fs-075" >12% of total</small>
                                </div>

                                <div class="category-item">
                                    <div class="category-header">
                                        <span class="category-name">Bonus</span>
                                        <span class="category-amount">$2,100</span>
                                    </div>
                                    <div class="category-bar">
                                        <div class="category-bar-fill w-25"></div>
                                    </div>
 <small class="text-surface-400 u-fs-075" >5% of total</small>
                                </div>

                                <div class="category-item">
                                    <div class="category-header">
                                        <span class="category-name">Refund</span>
                                        <span class="category-amount">$850</span>
                                    </div>
                                    <div class="category-bar">
                                        <div class="category-bar-fill" style="width: 15%;"></div>
                                    </div>
 <small class="text-surface-400 u-fs-075" >2% of total</small>
                                </div>

                                <div class="category-item">
                                    <div class="category-header">
                                        <span class="category-name">Other</span>
                                        <span class="category-amount">$200</span>
                                    </div>
                                    <div class="category-bar">
                                        <div class="category-bar-fill" style="width: 10%;"></div>
                                    </div>
 <small class="text-surface-400 u-fs-075" >0.4% of total</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                </div>
            </div>

            <!-- Monthly Trends -->
            <div class="row">
                <div class="col-12">
                    <div class="glassmorphism-card">
                        <div class="card-body">
                            <h5 class="section-title">
                                <i class="mdi mdi-chart-line me-2"></i>
                                Monthly Trends
                            </h5>

 <div class="d-flex align-items-center justify-content-center text-surface-400" style="height: 300px">
                                <div class="text-center">
 <i class="mdi mdi-chart-areaspline u-fs-4 text-gold u-opacity-50"></i>
                                    <p class="mt-3">Chart visualization would be displayed here<br>
                                    <small>Integrate with Chart.js or similar library</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#date_from", {
            dateFormat: "Y-m-d",
            defaultDate: new Date(new Date().getFullYear(), new Date().getMonth(), 1)
        });

        flatpickr("#date_to", {
            dateFormat: "Y-m-d",
            defaultDate: "today"
        });

        // Quick select handler
        document.getElementById('quick_select').addEventListener('change', function() {
            const value = this.value;
            const today = new Date();
            let fromDate, toDate = today;

            switch(value) {
                case 'today':
                    fromDate = today;
                    break;
                case 'week':
                    fromDate = new Date(today.setDate(today.getDate() - today.getDay()));
                    break;
                case 'month':
                    fromDate = new Date(today.getFullYear(), today.getMonth(), 1);
                    break;
                case 'quarter':
                    const quarter = Math.floor(today.getMonth() / 3);
                    fromDate = new Date(today.getFullYear(), quarter * 3, 1);
                    break;
                case 'year':
                    fromDate = new Date(today.getFullYear(), 0, 1);
                    break;
            }

            if (fromDate) {
                document.getElementById('date_from')._flatpickr.setDate(fromDate);
                document.getElementById('date_to')._flatpickr.setDate(new Date());
            }
        });
    </script>
@endsection
