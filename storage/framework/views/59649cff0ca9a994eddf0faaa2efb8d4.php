
<link rel="stylesheet" href="<?php echo e(URL::asset('build/libs/select2/css/select2.min.css')); ?>">
<link rel="stylesheet" href="<?php echo e(URL::asset('build/libs/bootstrap-datepicker/css/bootstrap-datepicker3.min.css')); ?>">
<style>
/* ── Select2 Theme-Aware ── */
.select2-container--default .select2-selection--single {
    background: var(--bs-card-bg, #fff);
    border: 1px solid rgba(212,175,55,.22);
    border-radius: 22px;
    height: 32px;
    padding: 0 .65rem;
    font-size: .75rem;
    color: var(--bs-body-color, #212529);
    transition: border-color .2s, box-shadow .2s;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: var(--bs-body-color, #212529);
    line-height: 30px;
    padding-left: 0;
    padding-right: 20px;
}
.select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: var(--text-secondary, #6b7280);
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 30px;
    right: 6px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow b {
    border-color: #b8860b transparent transparent transparent;
    border-width: 5px 4px 0 4px;
}
.select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
    border-color: transparent transparent #b8860b transparent;
    border-width: 0 4px 5px 4px;
}
.select2-container--default.select2-container--open .select2-selection--single,
.select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #d4af37;
    box-shadow: 0 0 0 2px rgba(212,175,55,.15);
    outline: none;
}
.select2-dropdown {
    background: var(--bs-card-bg, #fff);
    border: 1px solid rgba(212,175,55,.22);
    border-radius: .55rem;
    box-shadow: 0 6px 20px rgba(0,0,0,.12);
    overflow: hidden;
    z-index: 10060;
}
.select2-container--default .select2-search--dropdown .select2-search__field {
    background: var(--bs-card-bg, #fff);
    border: 1px solid rgba(212,175,55,.2);
    border-radius: .35rem;
    color: var(--bs-body-color, #212529);
    font-size: .75rem;
    padding: .35rem .55rem;
}
.select2-container--default .select2-search--dropdown .select2-search__field:focus {
    border-color: #d4af37;
    outline: none;
    box-shadow: 0 0 0 2px rgba(212,175,55,.12);
}
.select2-container--default .select2-results__option {
    font-size: .75rem;
    padding: .4rem .65rem;
    color: var(--bs-body-color, #212529);
    transition: background .15s;
}
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background: rgba(212,175,55,.12) !important;
    color: #b8860b !important;
}
.select2-container--default .select2-results__option[aria-selected=true] {
    background: rgba(212,175,55,.08) !important;
    color: #b8860b !important;
    font-weight: 600;
}
.select2-container--default .select2-results__option--selected {
    background: rgba(212,175,55,.08) !important;
    color: #b8860b !important;
    font-weight: 600;
}
.select2-results__options {
    max-height: 220px;
}
.select2-container--default .select2-results__option:hover {
    background: rgba(212,175,55,.06) !important;
}

/* ── Select2 inside filter bars (.pipe-pill) ── */
.pipe-filter-bar .select2-container {
    width: auto !important;
    min-width: 140px;
    max-width: 220px;
    flex-shrink: 0;
}
.pipe-filter-bar .select2-container--default .select2-selection--single {
    height: 28px;
    font-size: .72rem;
    border-color: rgba(212,175,55,.18);
}
.pipe-filter-bar .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 26px;
}
.pipe-filter-bar .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 26px;
}

/* ── Select2 inside modals ── */
.modal .select2-container--default .select2-selection--single {
    background: var(--bs-card-bg, #fff);
}
.modal .select2-dropdown {
    z-index: 10060;
}

/* ── Dark theme enhancements ── */
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .select2-dropdown {
    box-shadow: 0 8px 25px rgba(0,0,0,.4);
}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .select2-container--default .select2-results__option--highlighted[aria-selected] {
    color: #d4af37 !important;
}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .select2-container--default .select2-results__option[aria-selected=true],
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .select2-container--default .select2-results__option--selected {
    color: #d4af37 !important;
}

/* ── Bootstrap Datepicker Theme-Aware ── */
.datepicker {
    z-index: 10060 !important;
}
.datepicker table {
    border-collapse: separate;
    border-spacing: 2px;
}
.datepicker-dropdown {
    background: var(--bs-card-bg, #fff);
    border: 1px solid rgba(212,175,55,.22);
    border-radius: .6rem;
    box-shadow: 0 6px 20px rgba(0,0,0,.12);
    padding: .5rem;
    color: var(--bs-body-color, #212529);
}
.datepicker-dropdown:after,
.datepicker-dropdown:before {
    display: none;
}
.datepicker table tr td,
.datepicker table tr th {
    color: var(--bs-body-color, #212529);
    border-radius: .3rem;
    font-size: .72rem;
    width: 30px;
    height: 28px;
    text-align: center;
    transition: background .15s;
}
.datepicker table tr td:hover,
.datepicker table tr th:hover {
    background: rgba(212,175,55,.1) !important;
    color: #b8860b;
}
.datepicker table tr td.active,
.datepicker table tr td.active:hover,
.datepicker table tr td.active.highlighted,
.datepicker table tr td span.active {
    background: linear-gradient(135deg, #d4af37, #b8860b) !important;
    color: #fff !important;
    border: none;
    text-shadow: none;
}
.datepicker table tr td.today,
.datepicker table tr td.today:hover {
    background: rgba(212,175,55,.15) !important;
    color: #b8860b !important;
    border: none;
}
.datepicker table tr td.old,
.datepicker table tr td.new {
    opacity: .35;
}
.datepicker .datepicker-switch,
.datepicker .prev,
.datepicker .next {
    color: #b8860b !important;
    font-weight: 600;
    font-size: .78rem;
}
.datepicker .datepicker-switch:hover,
.datepicker .prev:hover,
.datepicker .next:hover {
    background: rgba(212,175,55,.1) !important;
    color: #8b6914 !important;
}
.datepicker table tr td span {
    border-radius: .3rem;
    color: var(--bs-body-color, #212529);
}
.datepicker table tr td span:hover {
    background: rgba(212,175,55,.1);
    color: #b8860b;
}
.datepicker .dow {
    color: #b8860b;
    font-weight: 600;
    font-size: .65rem;
    text-transform: uppercase;
}

/* Dark theme datepicker enhancements */
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .datepicker-dropdown {
    box-shadow: 0 8px 25px rgba(0,0,0,.4);
}
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .datepicker table tr td.old,
:is([data-theme="emerald-glass"],[data-theme="midnight-black"],[data-theme="ocean-blue"],[data-theme="royal-purple"],[data-theme="rose-gold"],[data-theme="copper-steel"]) .datepicker table tr td.new {
    opacity: .25;
}

/* ── Prevent page shift when Select2 opens ── */
.select2-container--open .select2-dropdown {
    max-width: 300px;
}
.select2-container--open .select2-dropdown--below,
.select2-container--open .select2-dropdown--above {
    width: auto !important;
    min-width: 100%;
}
</style>
<?php /**PATH /var/www/taurus-crm/resources/views/partials/custom-select-datepicker-styles.blade.php ENDPATH**/ ?>