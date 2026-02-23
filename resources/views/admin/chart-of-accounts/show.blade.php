@extends('layouts.master')
@section('title', 'View Account — ' . $account->account_name)
@section('css')
@include('partials.pipeline-dashboard-styles')
<style>
    .form-page-hdr{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.75rem;margin-bottom:.75rem}
    .form-page-hdr h4{font-size:1.1rem;font-weight:700;margin:0;display:flex;align-items:center;gap:.45rem}
    .form-page-hdr h4 i{color:#d4af37;font-size:1.25rem}
    .form-page-hdr p{margin:2px 0 0;font-size:.72rem;color:var(--bs-surface-500)}
    .detail-lbl{font-size:.68rem;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:var(--bs-surface-500);margin-bottom:.15rem}
    .detail-val{font-size:.82rem;font-weight:600;color:var(--bs-body-color);margin-bottom:.85rem}
    .balance-hero{font-size:1.55rem;font-weight:800;background:linear-gradient(135deg,#d4af37,#f0d878);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="form-page-hdr">
        <div>
            <h4><i class="bx bx-book-bookmark"></i> {{ $account->account_code }} — {{ $account->account_name }}</h4>
            <p>Account details & sub-accounts</p>
        </div>
        <div class="d-flex gap-2">
            @canEditModule('chart-of-accounts')
            <a href="{{ route('chart-of-accounts.edit', $account->id) }}" class="act-btn a-primary"><i class="bx bx-edit"></i> Edit</a>
            @endcanEditModule
            <a href="{{ route('chart-of-accounts.index') }}" class="act-btn a-info"><i class="bx bx-arrow-back"></i> Back</a>
        </div>
    </div>

    {{-- KPI Summary --}}
    <div class="kpi-row" style="grid-template-columns:repeat(auto-fill,minmax(180px,1fr))">
        <div class="kpi-card k-gold">
            <div class="kpi-lbl">Current Balance</div>
            <div class="kpi-val">${{ number_format($account->current_balance, 2) }}</div>
        </div>
        <div class="kpi-card k-blue">
            <div class="kpi-lbl">Opening Balance</div>
            <div class="kpi-val">${{ number_format($account->opening_balance, 2) }}</div>
        </div>
        <div class="kpi-card k-green">
            <div class="kpi-lbl">Status</div>
            <div class="kpi-val">
                <span class="s-pill {{ $account->is_active ? 's-active' : 's-closed' }}">{{ $account->is_active ? 'Active' : 'Inactive' }}</span>
            </div>
        </div>
        <div class="kpi-card k-purple">
            <div class="kpi-lbl">Sub-Accounts</div>
            <div class="kpi-val">{{ $account->childAccounts->count() }}</div>
        </div>
    </div>

    {{-- Account Details --}}
    <div class="ex-card sec-card">
        <div class="sec-hdr"><i class="bx bx-info-circle"></i> Account Information</div>
        <div class="sec-body">
            <div class="row g-3">
                <div class="col-md-4 col-sm-6">
                    <div class="detail-lbl">Account Code</div>
                    <div class="detail-val">{{ $account->account_code }}</div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="detail-lbl">Account Name</div>
                    <div class="detail-val">{{ $account->account_name }}</div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="detail-lbl">Account Type</div>
                    <div class="detail-val"><span class="v-badge">{{ $account->account_type }}</span></div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="detail-lbl">Category</div>
                    <div class="detail-val">{{ $account->account_category ?? '—' }}</div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="detail-lbl">Parent Account</div>
                    <div class="detail-val">
                        @if($account->parentAccount)
                            <a href="{{ route('chart-of-accounts.show', $account->parentAccount->id) }}" style="color:#d4af37;text-decoration:none;font-weight:600">
                                {{ $account->parentAccount->account_code }} — {{ $account->parentAccount->account_name }}
                            </a>
                        @else
                            <span style="opacity:.55">Top Level Account</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="detail-lbl">Current Balance</div>
                    <div class="detail-val"><span class="balance-hero">${{ number_format($account->current_balance, 2) }}</span></div>
                </div>
                @if($account->description)
                <div class="col-12">
                    <div class="detail-lbl">Description</div>
                    <div class="detail-val" style="color:var(--bs-surface-400)">{{ $account->description }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Sub-Accounts --}}
    @if($account->childAccounts->count() > 0)
    <div class="ex-card sec-card mt-2">
        <div class="sec-hdr"><i class="bx bx-folder-open"></i> Sub-Accounts ({{ $account->childAccounts->count() }})</div>
        <div class="sec-body p-0">
            <div class="table-responsive">
                <table class="ex-tbl">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th style="width:60px">View</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($account->childAccounts as $child)
                        <tr>
                            <td><strong>{{ $child->account_code }}</strong></td>
                            <td>{{ $child->account_name }}</td>
                            <td><span class="v-badge">{{ $child->account_type }}</span></td>
                            <td>${{ number_format($child->current_balance, 2) }}</td>
                            <td><span class="s-pill {{ $child->is_active ? 's-active' : 's-closed' }}">{{ $child->is_active ? 'Active' : 'Inactive' }}</span></td>
                            <td><a href="{{ route('chart-of-accounts.show', $child->id) }}" class="act-btn a-info" style="padding:.18rem .55rem"><i class="bx bx-show"></i></a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
