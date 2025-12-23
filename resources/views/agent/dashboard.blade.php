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
        body {
            background: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }
        .top-bar {
            background: linear-gradient(135deg, #d4af37 0%, #b8941f 100%);
            color: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .top-bar h4 {
            margin: 0;
            font-weight: 600;
        }
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border-left: 4px solid #d4af37;
        }
        .stat-card h2 {
            color: #d4af37;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .stat-card p {
            color: #6c757d;
            margin: 0;
            font-size: 0.875rem;
        }
        .table-wrapper {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .filter-bar {
            margin-bottom: 1.5rem;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
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
                <div class="stat-card" style="border-left-color: #28a745;">
                    <h2 style="color: #28a745;">{{ number_format($stats['approved']) }}</h2>
                    <p>Approved</p>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card" style="border-left-color: #dc3545;">
                    <h2 style="color: #dc3545;">{{ number_format($stats['declined']) }}</h2>
                    <p>Declined</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="border-left-color: #17a2b8;">
                    <h2 style="color: #17a2b8;">${{ number_format($stats['revenue'], 2) }}</h2>
                    <p>Total Revenue</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="border-left-color: #6c757d;">
                    <h2 style="color: #6c757d;">${{ number_format($stats['company_share'], 2) }}</h2>
                    <p>Company Share (30%)</p>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-wrapper">
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
