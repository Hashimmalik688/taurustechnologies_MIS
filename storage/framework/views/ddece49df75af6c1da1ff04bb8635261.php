<?php $__env->startSection('title', 'Accounting — Journal'); ?>

<?php $__env->startSection('css'); ?>
<style>
/* ── Accounting Module — Asset-Soft / Taurus Style ── */
:root {
    --acct-gold:        #d4af37;
    --acct-gold-dark:   #b8941f;
    --acct-gold-light:  #f5ecd0;
    --acct-dark:        #1a1a1a;
    --acct-header-bg:   #2d2d2d;
    --acct-surface:     #f8f9fa;
}

/* Module header bar */
.acct-module-header {
    background: var(--acct-header-bg);
    border-bottom: 3px solid var(--acct-gold);
    padding: 14px 20px;
    border-radius: 6px 6px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}
.acct-module-header .acct-module-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--acct-gold);
    letter-spacing: 0.03em;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.acct-module-header .acct-module-sub {
    font-size: 0.78rem;
    color: #aaa;
    margin: 0;
}

/* KPI tiles */
.acct-kpi-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1px;
    background: #dee2e6;
    border: 1px solid #dee2e6;
    border-top: none;
    border-radius: 0 0 6px 6px;
    overflow: hidden;
    margin-bottom: 20px;
}
@media (max-width: 768px) {
    .acct-kpi-row { grid-template-columns: repeat(2, 1fr); }
}
.acct-kpi-tile {
    background: #fff;
    padding: 14px 18px;
    position: relative;
}
.acct-kpi-tile::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 3px;
    background: var(--acct-gold);
}
.acct-kpi-tile .kpi-label {
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    color: #6c757d;
    margin-bottom: 4px;
}
.acct-kpi-tile .kpi-value {
    font-size: 1.25rem;
    font-weight: 700;
    font-family: 'Courier New', Courier, monospace;
    color: var(--acct-dark);
}
.acct-kpi-tile .kpi-value.kpi-dr   { color: #198754; }
.acct-kpi-tile .kpi-value.kpi-cr   { color: #dc3545; }
.acct-kpi-tile .kpi-value.kpi-gold { color: var(--acct-gold-dark); }
.acct-kpi-tile .kpi-icon {
    position: absolute;
    right: 14px; top: 50%;
    transform: translateY(-50%);
    font-size: 1.8rem;
    color: var(--acct-gold-light);
}

/* Filter bar */
.acct-filter-bar {
    background: var(--acct-surface);
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 10px 14px;
    margin-bottom: 14px;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
}
.acct-filter-bar label {
    font-size: 0.78rem;
    font-weight: 600;
    color: #6c757d;
    white-space: nowrap;
    margin: 0;
}

/* Journal table */
.acct-journal-card {
    border: 1px solid #dee2e6;
    border-radius: 6px;
    overflow: hidden;
}
.acct-journal-card .acct-table-header {
    background: var(--acct-header-bg);
    padding: 8px 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.acct-journal-card .acct-table-header span {
    font-size: 0.78rem;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: #ccc;
}
.acct-journal-card .acct-table-header .gold-line {
    flex: 1;
    height: 1px;
    background: linear-gradient(to right, var(--acct-gold), transparent);
    margin-left: 8px;
}
#journalTable thead th {
    background: #f1f3f5;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: #495057;
    border-bottom: 2px solid var(--acct-gold);
    white-space: nowrap;
}
#journalTable tbody tr:hover { background: #fffef5 !important; }
#journalTable td {
    font-size: 0.855rem;
    vertical-align: middle;
    border-color: #f1f3f5;
}
.acct-entry-num {
    font-family: 'Courier New', monospace;
    font-size: 0.82rem;
    color: var(--acct-gold-dark);
    font-weight: 600;
}
.acct-amount {
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
    color: #1a1a1a;
    font-weight: 600;
}

/* Type badges */
.acct-type-badge {
    display: inline-block;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    padding: 2px 8px;
    border-radius: 3px;
    white-space: nowrap;
}
.acct-badge-sale     { background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }
.acct-badge-payment  { background: #e3f2fd; color: #1565c0; border: 1px solid #90caf9; }
.acct-badge-opening  { background: #fff8e1; color: #f57f17; border: 1px solid #ffe082; }
.acct-badge-general  { background: #f3e5f5; color: #6a1b9a; border: 1px solid #ce93d8; }
.acct-badge-chargeback { background: #fce4ec; color: #b71c1c; border: 1px solid #ef9a9a; }

/* View button */
.btn-acct-view {
    font-size: 0.76rem;
    font-weight: 600;
    letter-spacing: 0.04em;
    padding: 3px 10px;
    border: 1px solid var(--acct-gold);
    color: var(--acct-gold-dark);
    background: transparent;
    border-radius: 3px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: background 0.15s, color 0.15s;
}
.btn-acct-view:hover {
    background: var(--acct-gold);
    color: #fff;
}

/* New entry button */
.btn-acct-new {
    background: var(--acct-gold);
    border: none;
    color: #1a1a1a;
    font-weight: 700;
    font-size: 0.82rem;
    padding: 6px 16px;
    border-radius: 4px 0 0 4px;
    letter-spacing: 0.03em;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    transition: background 0.15s;
}
.btn-acct-new:hover { background: var(--acct-gold-dark); color: #fff; }
.btn-acct-toggle {
    background: var(--acct-gold-dark);
    border: none;
    color: #1a1a1a;
    padding: 6px 10px;
    border-radius: 0 4px 4px 0;
    border-left: 1px solid rgba(0,0,0,.15);
    transition: background 0.15s;
}
.btn-acct-toggle:hover { background: var(--acct-gold); }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">

    
    <div class="acct-module-header">
        <div>
            <div class="acct-module-title">
                <i class="bx bx-book-open"></i>
                Accounting — General Ledger
            </div>
            <p class="acct-module-sub">All posted journal entries · Double-entry bookkeeping</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <a href="<?php echo e(route('admin.accounting.partner-ledger')); ?>"
               class="btn btn-sm btn-outline-secondary"
               style="font-size:0.8rem; color:#ccc; border-color:#555;">
                <i class="bx bx-user-circle me-1"></i> Partner Ledger
            </a>
            <?php if(auth()->check() && auth()->user()->canEditModule('accounting')): ?>
            <div class="btn-group">
                <a href="<?php echo e(route('admin.accounting.record-sale')); ?>" class="btn-acct-new">
                    <i class="bx bx-plus"></i> New Entry
                </a>
                <button type="button" class="btn-acct-toggle dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                    <span class="visually-hidden">More</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="font-size:0.85rem; min-width:180px">
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="<?php echo e(route('admin.accounting.record-sale')); ?>">
                            <i class="bx bx-purchase-tag text-success"></i> Record Sale
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="<?php echo e(route('admin.accounting.record-payment')); ?>">
                            <i class="bx bx-money text-primary"></i> Payment Received
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="<?php echo e(route('admin.accounting.record-chargeback')); ?>">
                            <i class="bx bx-undo" style="color:#b71c1c;"></i> ChargeBack / Sales Return
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="<?php echo e(route('admin.accounting.opening-balance')); ?>">
                            <i class="bx bx-reset text-warning"></i> Opening Balance
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="<?php echo e(route('admin.accounting.journal.create')); ?>">
                            <i class="bx bx-edit text-secondary"></i> General Journal Entry
                        </a>
                    </li>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="acct-kpi-row">
        <div class="acct-kpi-tile">
            <div class="kpi-label">Total Entries</div>
            <div class="kpi-value kpi-gold"><?php echo e(number_format($stats['entry_count'])); ?></div>
            <i class="bx bx-list-ul kpi-icon"></i>
        </div>
        <div class="acct-kpi-tile">
            <div class="kpi-label">Total Sales (Dr)</div>
            <div class="kpi-value kpi-dr"><?php echo e(number_format($stats['total_sales'], 2)); ?></div>
            <i class="bx bx-trending-up kpi-icon"></i>
        </div>
        <div class="acct-kpi-tile">
            <div class="kpi-label">Received (Cr)</div>
            <div class="kpi-value kpi-cr"><?php echo e(number_format($stats['total_payments'], 2)); ?></div>
            <i class="bx bx-wallet kpi-icon"></i>
        </div>
        <div class="acct-kpi-tile">
            <div class="kpi-label">Net Outstanding</div>
            <div class="kpi-value <?php echo e($stats['net_outstanding'] >= 0 ? 'kpi-dr' : 'kpi-cr'); ?>">
                <?php echo e(number_format(abs($stats['net_outstanding']), 2)); ?>

                <small style="font-size:0.65rem; font-family:sans-serif; color:#999; margin-left:3px">
                    <?php echo e($stats['net_outstanding'] >= 0 ? 'Dr' : 'Cr'); ?>

                </small>
            </div>
            <i class="bx bx-balance kpi-icon"></i>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show py-2 mb-3" role="alert"
             style="border-left: 4px solid #198754; border-radius: 4px; font-size:.875rem;">
            <i class="bx bx-check-circle me-1"></i><?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="acct-filter-bar">
        <label><i class="bx bx-filter-alt me-1"></i> Filter:</label>
        <select id="filterType" class="form-select form-select-sm" style="max-width:160px; font-size:.8rem">
            <option value="">All Types</option>
            <option value="sale">Sale</option>
            <option value="chargeback">Chargeback</option>
            <option value="payment_received">Payment Received</option>
            <option value="opening_balance">Opening Balance</option>
            <option value="general">General Journal</option>
        </select>
        <input type="date" id="filterDateFrom" class="form-control form-control-sm" style="max-width:136px; font-size:.8rem" title="From date">
        <input type="date" id="filterDateTo"   class="form-control form-control-sm" style="max-width:136px; font-size:.8rem" title="To date">
        <button id="btnFilter" class="btn btn-sm" style="background:var(--acct-gold);color:#1a1a1a;font-weight:600;font-size:.79rem;border:none;padding:4px 14px;">
            <i class="bx bx-search me-1"></i> Apply
        </button>
        <button id="btnToday" class="btn btn-sm btn-outline-secondary" style="font-size:.79rem;">
            <i class="bx bx-calendar-today me-1"></i> Today
        </button>
        <button id="btnClear" class="btn btn-sm btn-outline-secondary" style="font-size:.79rem;">
            Clear
        </button>
        <span class="ms-auto text-muted" style="font-size:0.75rem" id="recordCount"></span>
    </div>

    
    <div class="acct-journal-card">
        <div class="acct-table-header">
            <i class="bx bx-spreadsheet" style="color:var(--acct-gold); font-size:1rem;"></i>
            <span>Journal Entries</span>
            <div class="gold-line"></div>
        </div>
        <div class="table-responsive">
            <table id="journalTable" class="table table-hover mb-0 w-100">
                <thead>
                    <tr>
                        <th>Entry #</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Reference</th>
                        <th class="text-end">Amount (Dr)</th>
                        <th>Posted By</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script>
$(function () {
    var table = $('#journalTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?php echo e(route("admin.accounting.journal.index")); ?>',
            data: function (d) {
                d.type      = $('#filterType').val();
                d.date_from = $('#filterDateFrom').val();
                d.date_to   = $('#filterDateTo').val();
            }
        },
        columns: [
            {
                data: 'entry_number', name: 'entry_number',
                render: function(data) {
                    return '<span class="acct-entry-num">' + data + '</span>';
                }
            },
            { data: 'entry_date', name: 'entry_date',
              render: function(data) {
                  // Parse as local date to avoid UTC timezone offset issues
                  var p = data.split('-');
                  var m = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                  return p[2] + ' ' + (m[parseInt(p[1],10)-1] || p[1]) + ' ' + p[0];
              }
            },
            { data: 'type_badge', name: 'type', orderable: false },
            {
                data: 'description', name: 'description',
                render: function(data) {
                    return '<span style="font-size:.85rem;color:#495057">' + (data || '—') + '</span>';
                }
            },
            { data: 'reference', name: 'reference', defaultContent: '<span class="text-muted">—</span>' },
            {
                data: 'total_debit', name: 'total_debit',
                className: 'text-end',
                render: function (data) {
                    return '<span class="acct-amount">' +
                        parseFloat(data).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) +
                        '</span>';
                }
            },
            {
                data: 'creator', name: 'creator.name', orderable: false, searchable: false,
                render: function(data) {
                    return data ? ('<small class="text-muted">' + data.name + '</small>') : '<span class="text-muted">—</span>';
                }
            },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        language: {
            processing: '<span style="color:var(--acct-gold)">Loading…</span>',
            emptyTable: '<span class="text-muted" style="font-size:.85rem">No journal entries found.</span>',
            zeroRecords: '<span class="text-muted" style="font-size:.85rem">No matching entries.</span>',
        },
        drawCallback: function(settings) {
            var api  = this.api();
            var info = api.page.info();
            $('#recordCount').text(info.recordsTotal + ' total entries');
        }
    });

    $('#btnFilter').on('click', function () { table.ajax.reload(); });
    $('#btnToday').on('click', function () {
        var today = new Date().toISOString().slice(0, 10);
        $('#filterDateFrom').val(today);
        $('#filterDateTo').val(today);
        table.ajax.reload();
    });
    $('#btnClear').on('click', function () {
        $('#filterType').val('');
        $('#filterDateFrom').val('');
        $('#filterDateTo').val('');
        table.ajax.reload();
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/taurus-crm/resources/views/admin/accounting/journal/index.blade.php ENDPATH**/ ?>