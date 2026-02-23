<?php $__env->startSection('title'); ?>
    Reports
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
    .report-page { padding: 24px; }
    .report-header { margin-bottom: 24px; }
    .report-header h4 { font-size: 1.5rem; font-weight: 600; margin-bottom: 4px; display: flex; align-items: center; gap: 10px; }
    .report-header p { color: #6c757d; margin: 0; }

    /* Report Type Tabs */
    .report-type-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px; }
    .report-type-btn {
        padding: 8px 18px; border-radius: 8px; border: 1px solid #dee2e6;
        background: #fff; color: #495057; cursor: pointer; font-size: 0.875rem; font-weight: 500;
        transition: all 0.2s;
    }
    .report-type-btn:hover { border-color: #556ee6; color: #556ee6; }
    .report-type-btn.active { background: #556ee6; color: #fff; border-color: #556ee6; }

    /* Filter Card */
    .filter-card {
        background: #fff; border: 1px solid #e9ecef; border-radius: 12px;
        padding: 20px; margin-bottom: 20px;
    }
    .filter-card .filter-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 16px; cursor: pointer;
    }
    .filter-card .filter-header h6 { margin: 0; font-weight: 600; font-size: 0.95rem; display: flex; align-items: center; gap: 8px; }
    .filter-card .filter-header .toggle-icon { transition: transform 0.3s; }
    .filter-card .filter-header.collapsed .toggle-icon { transform: rotate(-90deg); }

    .filter-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 14px;
    }
    .filter-group label { font-size: 0.8rem; font-weight: 600; color: #495057; margin-bottom: 4px; display: block; }
    .filter-group select,
    .filter-group input {
        width: 100%; padding: 7px 12px; border: 1px solid #dee2e6; border-radius: 6px;
        font-size: 0.85rem; background: #fff; color: #212529;
    }
    .filter-group select:focus,
    .filter-group input:focus { border-color: #556ee6; outline: none; box-shadow: 0 0 0 2px rgba(85,110,230,0.15); }

    .filter-actions { display: flex; gap: 10px; margin-top: 16px; align-items: center; }
    .filter-actions .btn { padding: 8px 20px; font-size: 0.85rem; border-radius: 6px; }

    /* Summary Cards */
    .summary-row { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 14px; margin-bottom: 20px; }
    .summary-card {
        background: #fff; border: 1px solid #e9ecef; border-radius: 10px; padding: 16px;
        text-align: center;
    }
    .summary-card .summary-value { font-size: 1.4rem; font-weight: 700; color: #212529; }
    .summary-card .summary-label { font-size: 0.78rem; color: #6c757d; margin-top: 2px; }

    /* Results Table */
    .results-card { background: #fff; border: 1px solid #e9ecef; border-radius: 12px; overflow: hidden; }
    .results-card .results-header {
        padding: 16px 20px; border-bottom: 1px solid #e9ecef;
        display: flex; justify-content: space-between; align-items: center;
    }
    .results-card .results-header h6 { margin: 0; font-weight: 600; }
    .results-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
    .results-table thead th {
        padding: 10px 14px; text-align: left; font-weight: 600; color: #495057;
        border-bottom: 2px solid #e9ecef; white-space: nowrap; font-size: 0.8rem;
        cursor: pointer; user-select: none;
    }
    .results-table thead th:hover { color: #556ee6; }
    .results-table tbody td {
        padding: 10px 14px; border-bottom: 1px solid #f1f3f5; color: #212529;
        white-space: nowrap;
    }
    .results-table tbody tr:hover { background: #f8f9ff; }

    .status-badge {
        padding: 3px 10px; border-radius: 20px; font-size: 0.75rem;
        font-weight: 600; display: inline-block;
    }
    .status-sale { background: #d4edda; color: #155724; }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-declined { background: #f8d7da; color: #721c24; }
    .status-chargeback { background: #f8d7da; color: #721c24; }
    .status-accepted { background: #d1ecf1; color: #0c5460; }
    .status-default { background: #e9ecef; color: #495057; }

    .empty-state { padding: 60px 20px; text-align: center; color: #6c757d; }
    .empty-state i { font-size: 3rem; margin-bottom: 12px; display: block; color: #dee2e6; }

    .loading-overlay {
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(255,255,255,0.8); display: flex; align-items: center;
        justify-content: center; z-index: 10; border-radius: 12px;
    }
    .loading-overlay .spinner-border { width: 2.5rem; height: 2.5rem; }

    .table-responsive { overflow-x: auto; }

    @media (max-width: 768px) {
        .filter-grid { grid-template-columns: 1fr 1fr; }
        .summary-row { grid-template-columns: 1fr 1fr; }
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="report-page">
    <div class="report-header">
        <h4><i class="bx bx-bar-chart-alt-2"></i> Reports</h4>
        <p>Generate and export reports across leads, sales, partners, and more</p>
    </div>

    
    <div class="report-type-tabs">
        <button class="report-type-btn active" data-type="all">All Records</button>
        <button class="report-type-btn" data-type="sales">Sales Report</button>
        <button class="report-type-btn" data-type="partner">Partner Report</button>
        <button class="report-type-btn" data-type="submissions">Manager Submissions</button>
        <button class="report-type-btn" data-type="chargebacks">Chargebacks</button>
        <button class="report-type-btn" data-type="retention">Retention</button>
        <button class="report-type-btn" data-type="issuance">Issuance</button>
    </div>

    
    <div class="filter-card">
        <div class="filter-header" id="filterToggle">
            <h6><i class="bx bx-filter-alt"></i> Filters</h6>
            <i class="bx bx-chevron-down toggle-icon"></i>
        </div>
        <div class="filter-body" id="filterBody">
            <form id="reportForm">
                <input type="hidden" name="report_type" id="reportType" value="all">

                <div class="filter-grid">
                    
                    <div class="filter-group">
                        <label>Closer</label>
                        <select name="closer_id" id="closerFilter">
                            <option value="">All Closers</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $closers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($id); ?>"><?php echo e($name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>

                    
                    <div class="filter-group">
                        <label>Manager</label>
                        <select name="manager_id" id="managerFilter">
                            <option value="">All Managers</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $managers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($id); ?>"><?php echo e($name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>

                    
                    <div class="filter-group">
                        <label>Carrier</label>
                        <select name="carrier_id" id="carrierFilter">
                            <option value="">All Carriers</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $carriers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($id); ?>"><?php echo e($name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>

                    
                    <div class="filter-group">
                        <label>Partner</label>
                        <select name="partner_id" id="partnerFilter">
                            <option value="">All Partners</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $partners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($id); ?>"><?php echo e($name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>

                    
                    <div class="filter-group">
                        <label>Verifier</label>
                        <select name="verifier_id" id="verifierFilter">
                            <option value="">All Verifiers</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $verifiers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($id); ?>"><?php echo e($name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>

                    
                    <div class="filter-group">
                        <label>Lead Status</label>
                        <select name="status" id="statusFilter">
                            <option value="">All Statuses</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>

                    
                    <div class="filter-group">
                        <label>Team</label>
                        <select name="team" id="teamFilter">
                            <option value="">All Teams</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $teams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $team): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($team); ?>"><?php echo e($team); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>

                    
                    <div class="filter-group">
                        <label>Source</label>
                        <select name="source" id="sourceFilter">
                            <option value="">All Sources</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $sources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $source): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($source); ?>"><?php echo e($source); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>

                    
                    <div class="filter-group">
                        <label>State</label>
                        <select name="state" id="stateFilter">
                            <option value="">All States</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($state); ?>"><?php echo e($state); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </select>
                    </div>

                    
                    <div class="filter-group">
                        <label>QA Status</label>
                        <select name="qa_status" id="qaStatusFilter">
                            <option value="">All</option>
                            <option value="Good">Good</option>
                            <option value="Avg">Avg</option>
                            <option value="Bad">Bad</option>
                            <option value="In Review">In Review</option>
                            <option value="Pending">Pending</option>
                        </select>
                    </div>

                    
                    <div class="filter-group">
                        <label>Manager Status</label>
                        <select name="manager_status" id="managerStatusFilter">
                            <option value="">All</option>
                            <option value="approved">Approved</option>
                            <option value="pending">Pending</option>
                            <option value="declined">Declined</option>
                            <option value="underwriting">Underwriting</option>
                            <option value="chargeback">Chargeback</option>
                        </select>
                    </div>

                    
                    <div class="filter-group">
                        <label>Created From</label>
                        <input type="date" name="date_from" id="dateFrom">
                    </div>
                    <div class="filter-group">
                        <label>Created To</label>
                        <input type="date" name="date_to" id="dateTo">
                    </div>

                    
                    <div class="filter-group">
                        <label>Sale Date From</label>
                        <input type="date" name="sale_date_from" id="saleDateFrom">
                    </div>
                    <div class="filter-group">
                        <label>Sale Date To</label>
                        <input type="date" name="sale_date_to" id="saleDateTo">
                    </div>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-search-alt me-1"></i> Generate Report
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="resetFilters">
                        <i class="bx bx-reset me-1"></i> Reset
                    </button>
                    <button type="button" class="btn btn-outline-success" id="exportCsv">
                        <i class="bx bx-download me-1"></i> Export CSV
                    </button>
                </div>
            </form>
        </div>
    </div>

    
    <div class="summary-row" id="summaryRow" style="display: none;">
        <div class="summary-card">
            <div class="summary-value" id="summaryTotal">0</div>
            <div class="summary-label">Total Records</div>
        </div>
        <div class="summary-card">
            <div class="summary-value" id="summaryPremium">$0</div>
            <div class="summary-label">Total Premium</div>
        </div>
        <div class="summary-card">
            <div class="summary-value" id="summaryCoverage">$0</div>
            <div class="summary-label">Total Coverage</div>
        </div>
        <div class="summary-card">
            <div class="summary-value" id="summaryCommission">$0</div>
            <div class="summary-label">Total Commission</div>
        </div>
        <div class="summary-card">
            <div class="summary-value" id="summaryRevenue">$0</div>
            <div class="summary-label">Total Revenue</div>
        </div>
    </div>

    
    <div class="results-card" id="resultsCard" style="position: relative;">
        <div id="resultsContent">
            <div class="empty-state">
                <i class="bx bx-bar-chart"></i>
                <h6>Select filters and generate a report</h6>
                <p>Use the filters above to customize your report, then click "Generate Report"</p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('reportForm');
    const resultsContent = document.getElementById('resultsContent');
    const resultsCard = document.getElementById('resultsCard');
    const summaryRow = document.getElementById('summaryRow');
    const reportTypeInput = document.getElementById('reportType');

    // Report type tabs
    document.querySelectorAll('.report-type-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.report-type-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            reportTypeInput.value = this.dataset.type;
        });
    });

    // Toggle filters
    document.getElementById('filterToggle').addEventListener('click', function() {
        const body = document.getElementById('filterBody');
        const isVisible = body.style.display !== 'none';
        body.style.display = isVisible ? 'none' : 'block';
        this.classList.toggle('collapsed', isVisible);
    });

    // Generate report
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        loadReport();
    });

    // Reset filters
    document.getElementById('resetFilters').addEventListener('click', function() {
        form.reset();
        document.querySelectorAll('.report-type-btn').forEach(b => b.classList.remove('active'));
        document.querySelector('.report-type-btn[data-type="all"]').classList.add('active');
        reportTypeInput.value = 'all';
        summaryRow.style.display = 'none';
        resultsContent.innerHTML = `
            <div class="empty-state">
                <i class="bx bx-bar-chart"></i>
                <h6>Select filters and generate a report</h6>
                <p>Use the filters above to customize your report, then click "Generate Report"</p>
            </div>`;
    });

    // Export CSV
    document.getElementById('exportCsv').addEventListener('click', function() {
        const params = new URLSearchParams(new FormData(form));
        window.location.href = '<?php echo e(route("settings.reports.export")); ?>?' + params.toString();
    });

    // Pagination clicks (delegated)
    document.addEventListener('click', function(e) {
        const link = e.target.closest('#resultsContent .pagination a');
        if (link) {
            e.preventDefault();
            loadReport(link.href);
        }
    });

    function loadReport(url) {
        url = url || '<?php echo e(route("settings.reports.generate")); ?>';

        // Show loading
        const loader = document.createElement('div');
        loader.className = 'loading-overlay';
        loader.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
        resultsCard.appendChild(loader);

        const formData = new FormData(form);
        const params = new URLSearchParams(formData);

        // If URL already has query params (pagination), merge them
        const urlObj = new URL(url, window.location.origin);
        for (const [key, value] of params.entries()) {
            if (!urlObj.searchParams.has(key)) {
                urlObj.searchParams.set(key, value);
            }
        }

        fetch(urlObj.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        })
        .then(response => response.json())
        .then(data => {
            resultsContent.innerHTML = data.html;

            // Update summary
            if (data.summary) {
                summaryRow.style.display = 'grid';
                document.getElementById('summaryTotal').textContent = Number(data.summary.total_records).toLocaleString();
                document.getElementById('summaryPremium').textContent = '$' + Number(data.summary.total_premium).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                document.getElementById('summaryCoverage').textContent = '$' + Number(data.summary.total_coverage).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                document.getElementById('summaryCommission').textContent = '$' + Number(data.summary.total_commission).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                document.getElementById('summaryRevenue').textContent = '$' + Number(data.summary.total_revenue).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }
        })
        .catch(err => {
            resultsContent.innerHTML = `
                <div class="empty-state">
                    <i class="bx bx-error-circle"></i>
                    <h6>Error loading report</h6>
                    <p>${err.message || 'Something went wrong. Please try again.'}</p>
                </div>`;
        })
        .finally(() => {
            const overlay = resultsCard.querySelector('.loading-overlay');
            if (overlay) overlay.remove();
        });
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/reports/index.blade.php ENDPATH**/ ?>