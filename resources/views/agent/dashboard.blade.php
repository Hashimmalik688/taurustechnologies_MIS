<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Agent Dashboard | Taurus CRM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="shortcut icon" href="{{ URL::asset('images/favicon.ico') }}">
    <link href="{{ URL::asset('build/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    
    <style>
        /* ===== Modern Color Palette ===== */
        :root {
            --gradient-gold: linear-gradient(135deg, #f5af19 0%, #f12711 100%);
            --gradient-purple: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-blue: linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%);
            --gradient-green: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --gradient-red: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%);
            --gradient-teal: linear-gradient(135deg, #13547a 0%, #80d0c7 100%);
            --gradient-pink: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        body {
            background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            position: relative;
        }

        /* Animated Background Orbs */
        body::before, body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.4;
            z-index: -1;
            animation: float 20s infinite ease-in-out;
        }

        body::before {
            width: 500px;
            height: 500px;
            background: linear-gradient(135deg, #f5af19, #f12711);
            top: -250px;
            right: -250px;
        }

        body::after {
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            bottom: -200px;
            left: -200px;
            animation-delay: 10s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -30px) rotate(120deg); }
            66% { transform: translate(-20px, 20px) rotate(240deg); }
        }

        /* Top Bar */
        .top-bar {
            background: linear-gradient(135deg, rgba(245, 175, 25, 0.95) 0%, rgba(241, 39, 17, 0.95) 100%);
            color: white;
            padding: 1.25rem 2rem;
            box-shadow: 0 4px 20px rgba(245, 175, 25, 0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
            animation: slideDown 0.5s ease-out;
        }

        .top-bar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.08'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .top-bar > div {
            position: relative;
            z-index: 1;
        }

        .top-bar h4 {
            margin: 0;
            font-weight: 700;
            font-size: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .top-bar small {
            opacity: 0.9;
            font-size: 0.875rem;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Stat Cards */
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 1.25rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            animation: fadeInUp 0.6s ease-out backwards;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--gradient-gold);
            transition: width 0.3s ease;
        }

        .stat-card:hover::before {
            width: 100%;
            opacity: 0.1;
        }

        .stat-card h2 {
            color: #f5af19;
            font-weight: 800;
            margin-bottom: 0.25rem;
            font-size: 1.75rem;
            position: relative;
            z-index: 1;
        }

        .stat-card p {
            color: #6c757d;
            margin: 0;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }

        .stat-card-approved::before { background: var(--gradient-green); }
        .stat-card-approved h2 { color: #11998e; }

        .stat-card-declined::before { background: var(--gradient-red); }
        .stat-card-declined h2 { color: #ee0979; }

        .stat-card-revenue::before { background: var(--gradient-blue); }
        .stat-card-revenue h2 { color: #2193b0; }

        .stat-card-company::before { background: var(--gradient-teal); }
        .stat-card-company h2 { color: #13547a; }

        .stat-card-issued::before { background: var(--gradient-purple); }
        .stat-card-issued h2 { color: #667eea; }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Stagger animation delays */
        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }
        .stat-card:nth-child(5) { animation-delay: 0.5s; }
        .stat-card:nth-child(6) { animation-delay: 0.6s; }

        /* Table Wrapper */
        .table-wrapper {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: fadeInUp 0.6s ease-out 0.7s backwards;
        }

        .table-wrapper h5 {
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #f5af1920;
        }

        /* Filter Bar */
        .filter-bar {
            margin-bottom: 1.5rem;
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .filter-bar .form-control,
        .filter-bar .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .filter-bar .form-control:focus,
        .filter-bar .form-select:focus {
            border-color: #f5af19;
            box-shadow: 0 0 0 0.2rem rgba(245, 175, 25, 0.15);
        }

        /* Tables */
        .table {
            margin-bottom: 0;
        }

        .table thead {
            background: linear-gradient(135deg, rgba(245, 175, 25, 0.1), rgba(241, 39, 17, 0.1));
        }

        .table thead th {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 0.875rem;
            border: none;
            color: #f5af19;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table-hover tbody tr:hover {
            background: linear-gradient(135deg, rgba(245, 175, 25, 0.05), rgba(241, 39, 17, 0.05));
            transform: scale(1.01);
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .table tbody td {
            padding: 0.875rem;
            vertical-align: middle;
            font-size: 0.875rem;
        }

        /* Badge Styles */
        .badge {
            padding: 0.5rem 0.875rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.3px;
        }

        .bg-warning {
            background: linear-gradient(135deg, #f5af19, #f12711) !important;
        }

        .bg-success {
            background: linear-gradient(135deg, #11998e, #38ef7d) !important;
        }

        .bg-danger {
            background: linear-gradient(135deg, #ee0979, #ff6a00) !important;
        }

        .bg-info {
            background: linear-gradient(135deg, #2193b0, #6dd5ed) !important;
        }

        .bg-primary {
            background: linear-gradient(135deg, #667eea, #764ba2) !important;
        }

        /* Alerts */
        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.5rem;
            font-weight: 600;
            animation: slideInRight 0.5s ease-out;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(17, 153, 142, 0.15), rgba(56, 239, 125, 0.15));
            color: #11998e;
            border-left: 4px solid #11998e;
        }

        .alert-info {
            background: linear-gradient(135deg, rgba(33, 147, 176, 0.15), rgba(109, 213, 237, 0.15));
            color: #2193b0;
            border-left: 4px solid #2193b0;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Button Styles */
        .btn {
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-light {
            background: rgba(255, 255, 255, 0.95);
            color: #f5af19;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .btn-light:hover {
            background: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #495057);
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        }

        /* Table Footer */
        .table tfoot {
            background: linear-gradient(135deg, rgba(245, 175, 25, 0.05), rgba(241, 39, 17, 0.05));
            font-weight: 700;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .top-bar {
                padding: 1rem;
            }

            .top-bar h4 {
                font-size: 1.125rem;
            }

            .stat-card h2 {
                font-size: 1.5rem;
            }

            .filter-bar {
                flex-direction: column;
            }

            .filter-bar .form-control,
            .filter-bar .form-select {
                max-width: 100% !important;
            }
        }
    </style>
</head>

<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div>
            <h4>🏆 Agent Dashboard</h4>
            <small>Welcome, {{ $agent->name }}</small>
        </div>
        <div>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-light btn-sm">
                    <i class="bx bx-log-out"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <div class="container-fluid" style="padding: 2rem;">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-2">
                <div class="stat-card">
                    <h2>{{ number_format($stats['total_sales']) }}</h2>
                    <p>Total Sales</p>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card stat-card-approved">
                    <h2>{{ number_format($stats['approved']) }}</h2>
                    <p>Approved</p>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card stat-card-declined">
                    <h2>{{ number_format($stats['declined']) }}</h2>
                    <p>Declined</p>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card stat-card-revenue">
                    <h2>${{ number_format($stats['revenue'], 2) }}</h2>
                    <p>Total Revenue</p>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card stat-card-company">
                    <h2>${{ number_format($stats['company_share'], 2) }}</h2>
                    <p>Company Share (30%)</p>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card stat-card-issued">
                    <h2>{{ number_format($stats['issued_count']) }}</h2>
                    <p>Issued Apps</p>
                </div>
            </div>
        </div>

        <!-- Issued Applications Section -->
        @if($issuedLeads->count() > 0)
        <div class="table-wrapper mb-4">
            <h5 class="mb-3" style="color: #d4af37; font-weight: 600;">
                <i class="bx bx-check-circle me-2"></i>Issued Applications - Revenue Tracking
            </h5>
            
            <!-- Issued Stats Row -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="alert alert-success" role="alert">
                        <strong><i class="bx bx-dollar-circle me-2"></i>Total Revenue from Issued Apps:</strong> 
                        ${{ number_format($stats['issued_revenue'], 2) }}/month
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-info" role="alert">
                        <strong><i class="bx bx-shield me-2"></i>Total Coverage:</strong> 
                        ${{ number_format($stats['issued_coverage'], 0) }}
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-success">
                        <tr>
                            <th>Policy Number</th>
                            <th>Client Name</th>
                            <th>Phone</th>
                            <th>Carrier</th>
                            <th>Policy Type</th>
                            <th>Coverage</th>
                            <th>Monthly Premium</th>
                            <th>Issue Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($issuedLeads as $issued)
                            <tr>
                                <td>
                                    <span class="badge bg-primary">{{ $issued->issued_policy_number }}</span>
                                </td>
                                <td><strong>{{ $issued->cn_name }}</strong></td>
                                <td>{{ $issued->phone_number }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $issued->carrier_name ?? 'N/A' }}</span>
                                </td>
                                <td>{{ $issued->policy_type ?? 'N/A' }}</td>
                                <td class="text-success fw-semibold">${{ number_format($issued->coverage_amount ?? 0, 0) }}</td>
                                <td class="text-success fw-semibold">${{ number_format($issued->monthly_premium ?? 0, 2) }}</td>
                                <td>{{ $issued->issuance_date ? \Carbon\Carbon::parse($issued->issuance_date)->format('M d, Y') : 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-success">
                                        <i class="bx bx-check me-1"></i>Issued
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="5" class="text-end"><strong>Totals:</strong></td>
                            <td class="text-success fw-bold">${{ number_format($stats['issued_coverage'], 0) }}</td>
                            <td class="text-success fw-bold">${{ number_format($stats['issued_revenue'], 2) }}/mo</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif

        <!-- Table -->
        <div class="table-wrapper">
            <h5 class="mb-3" style="color: #6c757d; font-weight: 600;">
                <i class="bx bx-list-ul me-2"></i>All My Sales
            </h5>
            <div class="filter-bar">
                <input type="text" class="form-control" id="searchBox" placeholder="Search by name, phone..." style="max-width: 300px;">
                <select class="form-select" id="statusFilter" style="max-width: 200px;">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="accepted">Approved</option>
                    <option value="rejected">Declined</option>
                    <option value="forwarded">Forwarded</option>
                </select>
                <select class="form-select" id="carrierFilter" style="max-width: 200px;">
                    <option value="">All Carriers</option>
                    @foreach($leads->unique('carrier_name')->pluck('carrier_name')->filter() as $carrier)
                        <option value="{{ $carrier }}">{{ $carrier }}</option>
                    @endforeach
                </select>
                <button class="btn btn-secondary" onclick="clearFilters()">Clear Filters</button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped" id="leadsTable">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Client Name</th>
                            <th>Phone</th>
                            <th>DOB</th>
                            <th>Carrier</th>
                            <th>Coverage</th>
                            <th>Premium</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leads as $lead)
                            <tr data-status="{{ $lead->status }}" data-carrier="{{ $lead->carrier_name }}" data-name="{{ strtolower($lead->cn_name) }}" data-phone="{{ $lead->phone_number }}">
                                <td>{{ $lead->id }}</td>
                                <td>{{ $lead->date ?? 'N/A' }}</td>
                                <td><strong>{{ $lead->cn_name }}</strong></td>
                                <td>{{ $lead->phone_number }}</td>
                                <td>{{ $lead->date_of_birth ? \Carbon\Carbon::parse($lead->date_of_birth)->format('m/d/Y') : 'N/A' }}</td>
                                <td>{{ $lead->carrier_name ?? '—' }}</td>
                                <td>${{ number_format($lead->coverage_amount ?? 0, 0) }}</td>
                                <td>${{ number_format($lead->monthly_premium ?? 0, 2) }}</td>
                                <td>
                                    @if ($lead->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif ($lead->status == 'accepted')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif ($lead->status == 'rejected')
                                        <span class="badge bg-danger">Declined</span>
                                    @elseif ($lead->status == 'forwarded')
                                        <span class="badge bg-info">Forwarded</span>
                                    @else
                                        <span class="badge bg-secondary">Unknown</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr id="noResults">
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="bx bx-info-circle fs-3"></i>
                                    <p class="mb-0">No sales assigned to you yet</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $leads->links() }}
            </div>
        </div>
    </div>

    <script src="{{ URL::asset('build/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    
    <script>
        // Filter functionality
        const searchBox = document.getElementById('searchBox');
        const statusFilter = document.getElementById('statusFilter');
        const carrierFilter = document.getElementById('carrierFilter');
        const tableRows = document.querySelectorAll('#leadsTable tbody tr:not(#noResults)');

        function applyFilters() {
            const searchTerm = searchBox.value.toLowerCase();
            const selectedStatus = statusFilter.value;
            const selectedCarrier = carrierFilter.value;
            let visibleCount = 0;

            tableRows.forEach(row => {
                const name = row.getAttribute('data-name') || '';
                const phone = row.getAttribute('data-phone') || '';
                const status = row.getAttribute('data-status') || '';
                const carrier = row.getAttribute('data-carrier') || '';

                const matchesSearch = name.includes(searchTerm) || phone.includes(searchTerm);
                const matchesStatus = !selectedStatus || status === selectedStatus;
                const matchesCarrier = !selectedCarrier || carrier === selectedCarrier;

                if (matchesSearch && matchesStatus && matchesCarrier) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Show "no results" message if needed
            const noResults = document.getElementById('noResults');
            if (noResults) {
                noResults.style.display = visibleCount === 0 ? '' : 'none';
            }
        }

        searchBox.addEventListener('input', applyFilters);
        statusFilter.addEventListener('change', applyFilters);
        carrierFilter.addEventListener('change', applyFilters);

        function clearFilters() {
            searchBox.value = '';
            statusFilter.value = '';
            carrierFilter.value = '';
            applyFilters();
        }
    </script>
</body>
</html>
