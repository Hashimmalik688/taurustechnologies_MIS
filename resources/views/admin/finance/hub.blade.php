@extends('layouts.master')

@section('title')
    Finance & Accounts
@endsection

@section('css')
@include('components.hub-styles')
@endsection

@section('content')
    <div class="hub-page">
        <div class="hub-header">
            <h4><i class="bx bx-dollar-circle"></i> Finance & Accounts</h4>
            <p>Accounting, payroll, ledgers &amp; financial operations</p>
        </div>

        <div class="hub-section-label">Accounting</div>
        <div class="hub-grid">
            @canViewModule('chart-of-accounts')
            <a href="{{ route('chart-of-accounts.index') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-list-ul"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Chart of Accounts</div>
                    <p class="hub-card-desc">Manage account categories, codes &amp; organizational structure</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule

            @canViewModule('general-ledger')
            <a href="{{ route('ledger.index') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-book-open"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">General Ledger</div>
                    <p class="hub-card-desc">Financial transactions, journal entries &amp; account balances</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule

            @canViewModule('petty-cash')
            <a href="{{ route('petty-cash.index') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-wallet"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Petty Cash</div>
                    <p class="hub-card-desc">Track small expenditures, reimbursements &amp; fund balances</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule
        </div>

        <div class="hub-section-label">Payroll &amp; Tickets</div>
        <div class="hub-grid">
            @canViewModule('payroll')
            <a href="{{ route('payroll.index') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-credit-card-alt"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">Payroll</div>
                    <p class="hub-card-desc">Process salaries, generate payslips &amp; manage payroll periods</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule

            @canViewModule('pabs-tickets')
            <a href="{{ route('pabs.tickets.index') }}" class="hub-card">
                <div class="hub-card-icon"><i class="bx bx-message-square-error"></i></div>
                <div class="hub-card-body">
                    <div class="hub-card-title">PABS Tickets</div>
                    <p class="hub-card-desc">Payment &amp; billing support tickets, financial inquiries</p>
                </div>
                <i class="bx bx-chevron-right hub-card-arrow"></i>
            </a>
            @endcanViewModule
        </div>
    </div>
@endsection
