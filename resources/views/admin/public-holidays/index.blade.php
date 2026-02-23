@extends('layouts.master')

@section('title', 'Public Holidays')

@section('css')
@include('partials.pipeline-dashboard-styles')
<style>
    .ph-hdr {
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: .5rem; margin-bottom: .65rem;
    }
    .ph-hdr h4 { font-size: 1.1rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: .45rem; }
    .ph-hdr h4 i { color: #d4af37; font-size: 1.2rem; }
    .ph-hdr p { margin: 2px 0 0; font-size: .72rem; color: var(--bs-surface-500); }

    /* Upcoming holiday cards */
    .up-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: .65rem; margin-bottom: .85rem; }
    .up-card {
        background: #fff; border-radius: .55rem; padding: .85rem;
        border: 1px solid rgba(0,0,0,.04);
        box-shadow: 0 1px 4px rgba(0,0,0,.03);
        display: flex; gap: .65rem; align-items: flex-start;
        transition: all .2s ease;
    }
    .up-card:hover { transform: translateY(-2px); box-shadow: 0 4px 14px rgba(212,175,55,.10); border-color: rgba(212,175,55,.18); }
    .up-date {
        min-width: 48px; text-align: center; padding: .35rem .25rem;
        background: linear-gradient(135deg, rgba(212,175,55,.08), rgba(232,200,74,.06));
        border-radius: .4rem; flex-shrink: 0;
    }
    .up-date .ud-day { font-size: 1.3rem; font-weight: 800; color: #d4af37; line-height: 1; }
    .up-date .ud-month { font-size: .55rem; text-transform: uppercase; letter-spacing: .5px; color: var(--bs-surface-500); font-weight: 700; }
    .up-info { flex: 1; min-width: 0; }
    .up-info h6 { font-size: .78rem; font-weight: 700; margin: 0 0 .15rem; }
    .up-info .up-desc { font-size: .68rem; color: var(--bs-surface-500); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .up-badge { font-size: .55rem; padding: .15rem .4rem; border-radius: 10px; background: rgba(85,110,230,.08); color: #556ee6; font-weight: 700; white-space: nowrap; }

    /* Toggle button */
    .toggle-btn {
        background: none; border: none; padding: 0; cursor: pointer;
        transition: opacity .2s;
    }
    .toggle-btn:hover { opacity: .75; }
</style>
@endsection

@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 mb-2" role="alert" style="font-size:.78rem">
            <i class="mdi mdi-check-all me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Header -->
    <div class="ph-hdr">
        <div>
            <h4><i class="bx bx-calendar-star"></i> Public Holidays</h4>
            <p>Manage holiday calendar for attendance calculations</p>
        </div>
        <a href="{{ route('admin.public-holidays.create') }}" class="act-btn a-success"><i class="mdi mdi-plus"></i> Add Holiday</a>
    </div>

    <!-- KPI Row -->
    <div class="kpi-row">
        <div class="kpi-card k-gold">
            <span class="k-icon"><i class="bx bx-calendar"></i></span>
            <span class="k-val">{{ $holidays->total() }}</span>
            <span class="k-lbl">Total Holidays</span>
        </div>
        <div class="kpi-card k-green">
            <span class="k-icon"><i class="bx bx-check-circle"></i></span>
            <span class="k-val">{{ $upcomingHolidays->count() }}</span>
            <span class="k-lbl">Upcoming</span>
        </div>
        <div class="kpi-card k-blue">
            <span class="k-icon"><i class="bx bx-calendar-check"></i></span>
            <span class="k-val">{{ $holidays->where('is_active', true)->count() }}</span>
            <span class="k-lbl">Active</span>
        </div>
    </div>

    <!-- Upcoming Holidays -->
    @if($upcomingHolidays->count() > 0)
    <div class="ex-card sec-card" style="margin-bottom:.65rem">
        <div class="pipe-hdr">
            <i class="mdi mdi-calendar-star" style="color:#d4af37"></i> Upcoming Holidays
            <span class="badge-count">{{ $upcomingHolidays->count() }}</span>
        </div>
        <div class="sec-body">
            <div class="up-grid">
                @foreach($upcomingHolidays as $holiday)
                <div class="up-card">
                    <div class="up-date">
                        <div class="ud-day">{{ $holiday->date->format('d') }}</div>
                        <div class="ud-month">{{ $holiday->date->format('M') }}</div>
                    </div>
                    <div class="up-info">
                        <h6>{{ $holiday->name }}</h6>
                        @if($holiday->description)
                            <p class="up-desc" title="{{ $holiday->description }}">{{ Str::limit($holiday->description, 60) }}</p>
                        @endif
                    </div>
                    <span class="up-badge">{{ $holiday->date->diffForHumans() }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- All Holidays Table -->
    <div class="ex-card sec-card">
        <div class="pipe-hdr">
            <i class="mdi mdi-calendar-multiple"></i> All Holidays
            <span class="badge-count">{{ $holidays->total() }}</span>
        </div>
        <div class="sec-body" style="padding:0">
            @if($holidays->count() > 0)
            <div class="table-responsive">
                <table class="ex-tbl">
                    <thead>
                        <tr>
                            <th>Date</th><th>Day</th><th>Holiday Name</th><th>Description</th><th>Status</th><th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($holidays as $holiday)
                        <tr>
                            <td><span style="font-weight:700;font-size:.78rem">{{ $holiday->date->format('d M Y') }}</span></td>
                            <td><span class="v-badge v-blue">{{ $holiday->date->format('l') }}</span></td>
                            <td>
                                <span style="font-weight:600;font-size:.78rem">{{ $holiday->name }}</span>
                                @if($holiday->date->isPast())
                                    <span class="s-pill s-closed" style="font-size:.5rem;margin-left:.3rem">Past</span>
                                @elseif($holiday->date->isToday())
                                    <span class="s-pill s-sale" style="font-size:.5rem;margin-left:.3rem">Today</span>
                                @endif
                            </td>
                            <td>
                                @if($holiday->description)
                                    <span style="font-size:.72rem;color:var(--bs-surface-500)">{{ Str::limit($holiday->description, 50) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <form class="d-inline" action="{{ route('admin.public-holidays.toggle', $holiday) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="toggle-btn">
                                        @if($holiday->is_active)
                                            <span class="s-pill s-sale">Active</span>
                                        @else
                                            <span class="s-pill s-closed">Inactive</span>
                                        @endif
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    @canEditModule('holidays')
                                    <a href="{{ route('admin.public-holidays.edit', $holiday) }}" class="act-btn a-primary" title="Edit"><i class="mdi mdi-pencil"></i></a>
                                    @endcanEditModule
                                    @canDeleteInModule('holidays')
                                    <form action="{{ route('admin.public-holidays.destroy', $holiday) }}" method="POST" onsubmit="return confirm('Delete this holiday?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="act-btn a-danger" title="Delete"><i class="mdi mdi-delete"></i></button>
                                    </form>
                                    @endcanDeleteInModule
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="padding:.5rem .75rem">{{ $holidays->links() }}</div>
            @else
            <div class="text-center py-4">
                <i class="mdi mdi-calendar-remove" style="font-size:2rem;color:var(--bs-surface-300)"></i>
                <p class="text-muted mt-1" style="font-size:.78rem">No holidays configured yet.</p>
                <a href="{{ route('admin.public-holidays.create') }}" class="act-btn a-success"><i class="mdi mdi-plus"></i> Add Holiday</a>
            </div>
            @endif
        </div>
    </div>
@endsection

@section('script')
@endsection
