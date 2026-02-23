@extends('layouts.master')

@section('title', 'Create Ticket - PABS')

@section('css')
@include('partials.pipeline-dashboard-styles')
@include('partials.custom-select-datepicker-styles')
<style>
    .crm-label{display:block;font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:#8c8c8c;margin-bottom:.35rem}
    .crm-label.required::after{content:" *";color:#ef4444}
    .crm-input{border-radius:22px;border:1px solid #e2e2e2;padding:.45rem 1rem;font-size:.82rem;width:100%;transition:border .2s,box-shadow .2s;background:var(--bs-card-bg,#fff);color:var(--bs-body-color)}
    .crm-input:focus{border-color:#b8860b;box-shadow:0 0 0 3px rgba(184,134,11,.12);outline:none}
    select.crm-input{appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23b8860b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right .7rem center;padding-right:1.8rem}
    textarea.crm-input{border-radius:.6rem;min-height:120px}
    .crm-prefix{display:flex;align-items:center;gap:0}
    .crm-prefix .pfx{background:linear-gradient(135deg,#b8860b 0%,#d4a843 100%);color:#fff;border-radius:22px 0 0 22px;padding:.45rem .85rem;font-size:.78rem;font-weight:600;white-space:nowrap}
    .crm-prefix .crm-input{border-radius:0 22px 22px 0;border-left:0}
    .info-card{border-radius:14px;border:1px solid rgba(184,134,11,.18);background:linear-gradient(135deg,rgba(184,134,11,.04) 0%,rgba(212,168,67,.02) 100%)}
    .info-card .info-hdr{padding:.85rem 1.1rem;border-bottom:1px solid rgba(184,134,11,.12);font-size:.82rem;font-weight:700;color:#b8860b}
    .info-card .info-body{padding:1rem 1.1rem;font-size:.78rem;color:var(--bs-body-color)}
    .info-card .info-body p{margin-bottom:.5rem}
    .info-card .info-body ul{padding-left:1.1rem;margin-bottom:.6rem}
    .info-card .info-body li{margin-bottom:.25rem}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row g-4">
        {{-- Form --}}
        <div class="col-lg-8">
            <div class="sec-card">
                <div class="sec-hdr"><i class="bx bx-edit" style="color:#b8860b"></i> Submit Support Ticket</div>
                <div class="sec-body">
                    <form action="{{ route('pabs.tickets.store') }}" method="POST">
                        @csrf

                        {{-- Section --}}
                        <div style="margin-bottom:1.1rem">
                            <label class="crm-label required">Department / Section</label>
                            <select name="section_id" id="section_id" class="crm-input @error('section_id') is-invalid @enderror" required>
                                <option value="">— Select Section —</option>
                                @foreach($sections as $id => $name)
                                    <option value="{{ $id }}" {{ old('section_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('section_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Subject --}}
                        <div style="margin-bottom:1.1rem">
                            <label class="crm-label required">Subject</label>
                            <input type="text" name="subject" class="crm-input @error('subject') is-invalid @enderror" placeholder="Brief description of the issue" value="{{ old('subject') }}" required>
                            @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Description --}}
                        <div style="margin-bottom:1.1rem">
                            <label class="crm-label required">Description</label>
                            <textarea name="description" class="crm-input @error('description') is-invalid @enderror" rows="6" placeholder="Provide detailed information about the issue…" required>{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row g-3">
                            {{-- Priority --}}
                            <div class="col-md-6">
                                <div style="margin-bottom:1.1rem">
                                    <label class="crm-label required">Priority</label>
                                    <select name="priority" class="crm-input @error('priority') is-invalid @enderror" required>
                                        <option value="MEDIUM" {{ old('priority','MEDIUM') == 'MEDIUM' ? 'selected' : '' }}>Medium</option>
                                        <option value="HIGH" {{ old('priority') == 'HIGH' ? 'selected' : '' }}>High</option>
                                        <option value="LOW" {{ old('priority') == 'LOW' ? 'selected' : '' }}>Low</option>
                                    </select>
                                    @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- Assign To --}}
                            <div class="col-md-6">
                                <div style="margin-bottom:1.1rem">
                                    <label class="crm-label required">Assign To</label>
                                    <select name="assigned_to" class="crm-input @error('assigned_to') is-invalid @enderror" required>
                                        <option value="">— Select User —</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('assigned_to')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            {{-- Estimated Budget --}}
                            <div class="col-md-6">
                                <div style="margin-bottom:1.1rem">
                                    <label class="crm-label">Estimated Budget</label>
                                    <div class="crm-prefix">
                                        <span class="pfx">PKR</span>
                                        <input type="number" name="total_cost" class="crm-input @error('total_cost') is-invalid @enderror" placeholder="0.00" step="0.01" min="0" value="{{ old('total_cost') }}">
                                    </div>
                                    @error('total_cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- Quote Amount --}}
                            <div class="col-md-6">
                                <div style="margin-bottom:1.1rem">
                                    <label class="crm-label">Quote / Amount</label>
                                    <div class="crm-prefix">
                                        <span class="pfx">PKR</span>
                                        <input type="number" name="quote_amount" class="crm-input @error('quote_amount') is-invalid @enderror" placeholder="0.00" step="0.01" min="0" value="{{ old('quote_amount') }}">
                                    </div>
                                    @error('quote_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="row g-3" style="margin-top:.6rem">
                            <div class="col-md-6">
                                <a href="{{ route('pabs.tickets.index') }}" class="act-btn a-warn" style="width:100%;text-align:center;display:block"><i class="bx bx-arrow-back"></i> Cancel</a>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="act-btn a-success" style="width:100%"><i class="bx bx-send"></i> Submit Ticket</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Info Sidebar --}}
        <div class="col-lg-4">
            <div class="info-card">
                <div class="info-hdr"><i class="bx bx-info-circle"></i> Ticket System Info</div>
                <div class="info-body">
                    <p><strong>What is this?</strong></p>
                    <p>Use this system to report issues, request support, or coordinate work related to any departmental function.</p>

                    <p><strong>Priority Levels:</strong></p>
                    <ul>
                        <li><strong style="color:#ef4444">High:</strong> Urgent, blocking work</li>
                        <li><strong style="color:#f59e0b">Medium:</strong> Standard request</li>
                        <li><strong style="color:#10b981">Low:</strong> Non-urgent, can wait</li>
                    </ul>

                    <p><strong>Available Sections:</strong></p>
                    <ul style="margin-bottom:0">
                        @foreach($sections as $id => $name)
                            <li>{{ $name }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('build/libs/select2/js/select2.min.js') }}"></script>
<script>$(function(){$('select.crm-input').select2({minimumResultsForSearch:10,width:'100%'})});</script>
@endsection