@extends('layouts.master')

@section('title')
    Agent Dashboard
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Dashboard
        @endslot
        @slot('title')
            Agent Dashboard
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <h4 class="mb-4">Welcome, {{ $agent->name }}!</h4>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-2">Total Leads</p>
                    <h3>{{ $stats['total_leads'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-2">Today's Leads</p>
                    <h3>{{ $stats['today_leads'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-2">Pending</p>
                    <h3>{{ $stats['pending'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-2">Closed</p>
                    <h3>{{ $stats['closed'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center p-5">
                    <h3>Your Agent Dashboard</h3>
                    <p class="text-muted">View and manage your leads from here.</p>
                    <a href="{{ route('sales.index') }}" class="btn btn-primary">View My Leads</a>
                </div>
            </div>
        </div>
    </div>

@endsection
