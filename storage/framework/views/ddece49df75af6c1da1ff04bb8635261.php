<?php $__env->startSection('title', 'Journal Entries'); ?>

<?php $__env->startSection('css'); ?>
<link href="<?php echo e(URL::asset('/build/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')); ?>" rel="stylesheet" />
<link href="<?php echo e(URL::asset('/build/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')); ?>" rel="stylesheet" />
<style>
:root {
    --acct-gold:    #d4af37;
    --acct-gold-dk: #b8941f;
    --acct-surface: #f5f6fa;
    --acct-border:  #e8eaed;
    --acct-text:    #1a1a2e;
    --acct-muted:   #6b7280;
}
body { background: var(--acct-surface); }
.acct-page { padding: 24px 24px 40px; }

/* Page header */
.jnl-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 22px;
}
.jnl-page-title { font-size: 1.25rem; font-weight: 800; color: var(--acct-text); margin: 0 0 2px; }
.jnl-page-sub   { font-size: .8rem; color: var(--acct-muted); margin: 0; }

/* KPI strip */
.jnl-kpi-strip {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 22px;
}
@media(max-width:800px){ .jnl-kpi-strip{ grid-template-columns: repeat(2,1fr); } }
.jnl-kpi {
    background: #fff;
    border: 1px solid var(--acct-border);
    border-radius: 8px;
    padding: 14px 18px;
    position: relative;
    overflow: hidden;
}
.jnl-kpi::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0;
    height: 3px; border-radius: 8px 8px 0 0;
    background: var(--acct-gold);
}
.jnl-kpi-label { font-size: .68rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--acct-muted); margin-bottom: 4px; }
.jnl-kpi-val   { font-size: 1.3rem; font-weight: 800; color: var(--acct-text); font-family: 'Inter',system-ui,sans-serif; }
.jnl-kpi-val.green  { color: #059669; }
.jnl-kpi-val.red    { color: #dc2626; }
.jnl-kpi-val.indigo { color: #4f46e5; }
.jnl-kpi-icon { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); font-size: 1.9rem; opacity: .1; }

/* Filter bar */
.jnl-filter-bar {
    background: #fff;
    border: 1px solid var(--acct-border);
    border-radius: 8px;
    padding: 12px 18px;
    margin-bottom: 18px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
}
.jnl-filter-bar label { font-size: .73rem; font-weight: 700; color: var(--acct-muted); white-space: nowrap; margin: 0; }
.jnl-filter-bar .form-select, .jnl-filter-bar .form-control {
    font-size: .82rem; border: 1px solid #d1d5db; border-radius: 6px; padding: 5px 10px; height: auto;
}
.jnl-filter-bar .form-select:focus, .jnl-filter-bar .form-control:focus {
    border-color: var(--acct-gold); box-shadow: 0 0 0 3px rgba(212,175,55,.15);
}

/* Journal table card */
.jnl-card {
    background: #fff;
    border: 1px solid var(--acct-border);
    border-radius: 10px;
    overflow: hidden;
}
.jnl-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 13px 20px;
    border-bottom: 1px solid var(--acct-border);
    background: #fafbfc;
}
.jnl-card-title {
    font-size: .82rem; font-weight: 700; letter-spacing: .05em;
    text-transform: uppercase; color: #374151;
    display: flex; align-items: center; gap: 7px;
}
.jnl-card-title i { color: var(--acct-gold); }

/* Override DataTables styling */
div.dataTables_wrapper div.dataTables_length,
div.dataTables_wrapper div.dataTables_info { display: none; }
div.dataTables_wrapper div.dataTables_filter {
    text-align: right;
    padding: 10px 16px 6px;
}
div.dataTables_wrapper div.dataTables_filter label {
    font-size: .75rem; font-weight: 600; color: var(--acct-muted);
}
div.dataTables_wrapper div.dataTables_filter input {
    border: 1px solid #d1d5db; border-radius: 6px; font-size: .8rem;
    padding: 4px 10px; margin-left: 6px;
}
div.dataTables_wrapper div.dataTables_filter input:focus {
    border-color: var(--acct-gold); outline: none;
    box-shadow: 0 0 0 2px rgba(212,175,55,.15);
}
div.dataTables_wrapper div.dataTables_paginate {
    padding: 12px 16px;
    border-top: 1px solid var(--acct-border);
}
div.dataTables_wrapper div.dataTables_paginate .paginate_button.current {
    background: var(--acct-gold) !important;
    border: none !important; color: #1a1a2e !important;
    border-radius: 5px !important; font-weight: 700;
}
div.dataTables_wrapper div.dataTables_paginate .paginate_button:hover {
    background: rgba(212,175,55,.15) !important;
    border: 1px solid rgba(212,175,55,.3) !important;
    color: var(--acct-gold-dk) !important; border-radius: 5px !important;
}
div.dataTables_wrapper div.dataTables_paginate .paginate_button {
    font-size: .78rem; padding: 4px 10px !important;
}

/* Table itself */
#journalTable { border: none !important; }
#journalTable thead th {
    background: #fafbfc;
    font-size: .68rem;
    font-weight: 700;
    letter-spacing: .07em;
    text-transform: uppercase;
    color: var(--acct-muted);
    border-bottom: 2px solid var(--acct-border) !important;
    border-top: none !important;
    padding: 10px 16px;
    white-space: nowrap;
}
#journalTable tbody td {
    padding: 11px 16px;
    border-color: #f3f4f6 !important;
    vertical-align: middle;
    font-size: .84rem;
}
#journalTable tbody tr:hover td { background: #fffef5 !important; }

.entry-num {
    font-family: 'Courier New', monospace;
    font-size: .78rem; font-weight: 700;
    color: var(--acct-gold-dk); text-decoration: none;
}
.entry-num:hover { text-decoration: underline; }

.acct-type-badge {
    display: inline-block; font-size: .65rem; font-weight: 700;
    letter-spacing: .04em; text-transform: uppercase;
    padding: 2px 8px; border-radius: 3px; white-space: nowrap;
}
.acct-badge-sale       { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.acct-badge-payment    { background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; }
.acct-badge-opening    { background: #fef9c3; color: #92400e; border: 1px solid #fde68a; }
.acct-badge-general    { background: #ede9fe; color: #5b21b6; border: 1px solid #ddd6fe; }
.acct-badge-chargeback { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

.acct-amount {
    font-family: 'Courier New', monospace;
    font-size: .88rem; font-weight: 700; color: var(--acct-text);
}

.btn-acct-view {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: .75rem; font-weight: 600; padding: 4px 10px;
    border: 1px solid var(--acct-gold); color: var(--acct-gold-dk);
    background: transparent; border-radius: 5px; text-decoration: none;
    transition: background .15s, color .15s;
}
.btn-acct-view:hover { background: var(--acct-gold); color: #fff; }

#recordCount { font-size: .75rem; color: var(--acct-muted); }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.accounting._nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="acct-page">

    
    <div class="jnl-page-header">
        <div>
            <h1 class="jnl-page-title">Journal Entries</h1>
            <p class="jnl-page-sub">General ledger — all posted double-entry transactions</p>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show py-2 mb-3" style="border-left:4px solid #059669;font-size:.875rem;border-radius:6px">
            <i class="bx bx-check-circle me-1"></i><?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="jnl-kpi-strip">
        <div class="jnl-kpi">
            <div class="jnl-kpi-label">Total Entries</div>
            <div class="jnl-kpi-val"><?php echo e(number_format($stats['entry_count'])); ?></div>
            <i class="bx bx-list-ul jnl-kpi-icon"></i>
        </div>
        <div class="jnl-kpi">
            <div class="jnl-kpi-label">Total Sales</div>
            <div class="jnl-kpi-val green">$<?php echo e(number_format($stats['total_sales'], 2)); ?></div>
            <i class="bx bx-trending-up jnl-kpi-icon"></i>
        </div>
        <div class="jnl-kpi">
            <div class="jnl-kpi-label">Payments Received</div>
            <div class="jnl-kpi-val indigo">$<?php echo e(number_format($stats['total_payments'], 2)); ?></div>
            <i class="bx bx-money jnl-kpi-icon"></i>
        </div>
        <div class="jnl-kpi">
            <div class="jnl-kpi-label">Net Outstanding</div>
            <div class="jnl-kpi-val <?php echo e($stats['net_outstanding'] >= 0 ? 'green' : 'red'); ?>">
                $<?php echo e(number_format(abs($stats['net_outstanding']), 2)); ?>

                <small style="font-size:.65rem;font-weight:500;color:var(--acct-muted)"><?php echo e($stats['net_outstanding'] >= 0 ? 'Dr' : 'Cr'); ?></small>
            </div>
            <i class="bx bx-balance jnl-kpi-icon"></i>
        </div>
    </div>

    
    <div class="jnl-filter-bar">
        <label><i class="bx bx-filter-alt me-1"></i> Filter:</label>
        <select id="filterType" class="form-select" style="max-width:170px">
            <option value="">All Types</option>
            <option value="sale">Sale</option>
            <option value="chargeback">Chargeback</option>
            <option value="payment_received">Payment Received</option>
            <option value="opening_balance">Opening Balance</option>
            <option value="general">General Journal</option>
        </select>
        <input type="date" id="filterDateFrom" class="form-control" style="max-width:145px" title="From date">
        <input type="date" id="filterDateTo"   class="form-control" style="max-width:145px" title="To date">
        <button id="btnFilter" class="btn btn-sm" style="background:var(--acct-gold);color:#1a1a2e;font-weight:700;font-size:.79rem;border:none;padding:5px 16px">
            <i class="bx bx-search me-1"></i> Apply
        </button>
        <button id="btnToday" class="btn btn-sm btn-outline-secondary" style="font-size:.79rem">
            <i class="bx bx-calendar-today me-1"></i> Today
        </button>
        <button id="btnClear" class="btn btn-sm btn-outline-secondary" style="font-size:.79rem">Clear</button>
        <span class="ms-auto" id="recordCount"></span>
    </div>

    
    <div class="jnl-card">
        <div class="jnl-card-header">
            <span class="jnl-card-title"><i class="bx bx-spreadsheet"></i> All Journal Entries</span>
        </div>
        <div style="overflow-x:auto">
            <table id="journalTable" class="table mb-0 w-100">
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
<script src="<?php echo e(URL::asset('build/libs/datatables.net/js/jquery.dataTables.min.js')); ?>"></script>
<script src="<?php echo e(URL::asset('build/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js')); ?>"></script>
<script>
$(function () {
    var table = $('#journalTable').DataTable({
        destroy: true,
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
                render: function(data, type, row) {
                    return '<a href="/admin/accounting/journal/' + row.id + '" class="entry-num">' + (data||'') + '</a>';
                }
            },
            { data: 'entry_date', name: 'entry_date',
              render: function(data) {
                  var p = (data||'').split('-');
                  var m = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                  return p[2] + ' ' + (m[parseInt(p[1],10)-1] || p[1]) + ' ' + p[0];
              }
            },
            { data: 'type_badge', name: 'type', orderable: false },
            {
                data: 'description', name: 'description',
                render: function(data) {
                    return '<span style="color:#374151;max-width:240px;display:inline-block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;vertical-align:middle">' + (data||'—') + '</span>';
                }
            },
            { data: 'reference', name: 'reference', defaultContent: '<span style="color:#9ca3af">—</span>' },
            {
                data: 'total_debit', name: 'total_debit', className: 'text-end',
                render: function(data) {
                    return '<span class="acct-amount">$' + parseFloat(data||0).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2}) + '</span>';
                }
            },
            {
                data: 'creator', name: 'creator.name', orderable: false, searchable: false,
                render: function(data) {
                    return data ? '<small style="color:var(--acct-muted)">' + data.name + '</small>' : '<span style="color:#9ca3af">—</span>';
                }
            },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        dom: 'frtip',
        language: {
            processing: '<span style="color:var(--acct-gold)">Loading…</span>',
            emptyTable: '<span style="color:var(--acct-muted);font-size:.85rem">No journal entries found.</span>',
            zeroRecords: '<span style="color:var(--acct-muted);font-size:.85rem">No matching entries.</span>',
            search: 'Search:',
        },
        drawCallback: function() {
            var info = this.api().page.info();
            $('#recordCount').text(info.recordsTotal.toLocaleString() + ' total entries');
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