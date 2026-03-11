@extends('layouts.master')

@section('title')
    Leads Hub
@endsection

@section('css')
@include('components.hub-styles')
@endsection

@section('content')
    <div class="hub-page">
        <div class="hub-header">
            <h4><i class="bx bx-clipboard"></i> Leads</h4>
            <p>Peregrine leads, Raven leads &amp; bad lead management</p>
        </div>

        @php $user = auth()->user(); @endphp

        @if($user->canViewModule('leads-peregrine') || $user->canViewModule('leads'))
        <div class="hub-section-label">Team Leads</div>
        <div class="hub-grid">
            @canViewModule('leads-peregrine')
            <a href="{{ route('leads.peregrine') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-user-voice"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Peregrine Leads</div>
                    <p class="hub-card-desc">View and manage all Peregrine team leads and applications</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule

            @canViewModule('leads')
            <a href="{{ route('leads.index') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-briefcase"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Raven Leads</div>
                    <p class="hub-card-desc">View and manage all Ravens team leads and follow-ups</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule
        </div>
        @endif

        @canViewModule('ravens-bad-leads')
        <div class="hub-section-label">Lead Management</div>
        <div class="hub-grid">
            <a href="{{ route('ravens.bad-leads') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-x-circle"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Bad Leads</div>
                    <p class="hub-card-desc">Review and manage rejected or unqualified leads</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
        </div>
        @endcanViewModule
    </div>
@endsection
