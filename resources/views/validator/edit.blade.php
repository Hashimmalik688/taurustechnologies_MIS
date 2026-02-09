@extends('layouts.master')

@section('title')
    Edit Lead - Validator
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Validator @endslot
        @slot('title') Edit Lead @endslot
    @endcomponent

    <form method="POST" action="{{ route('validator.update', $lead->id) }}">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info">
                        <h5 class="mb-0">
                            <i class="bx bx-edit me-2"></i>
                            Validate Lead - {{ $lead->cn_name }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @include('peregrine.closers.form', ['lead' => $lead, 'isValidator' => true])
                        
                        <div class="mt-4 pt-3 border-top d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bx bx-check me-2"></i>
                                Mark as Sale
                            </button>
                            <form method="POST" action="{{ route('validator.mark-forwarded', $lead->id) }}" class="d-inline">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-warning btn-lg" onclick="return confirm('Send this lead to Home Office?')">
                                    <i class="bx bx-send me-2"></i>
                                    Pending:Sent to Home Office
                                </button>
                            </form>
                            <button type="button" class="btn btn-danger btn-lg" data-bs-toggle="modal" data-bs-target="#failModal">
                                <i class="bx bx-x me-2"></i>
                                Mark as Failed
                            </button>
                            <form method="POST" action="{{ route('validator.return-to-closer', $lead->id) }}" class="d-inline">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-secondary btn-lg" onclick="return confirm('Return this lead to closer for more information?')">
                                    <i class="bx bx-arrow-back me-2"></i>
                                    Return to Closer
                                </button>
                            </form>
                            <a href="{{ route('validator.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bx bx-x me-1"></i>
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Failure Reason Modal -->
    <div class="modal fade" id="failModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title">Select Failure Reason</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('validator.mark-failed', $lead->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <p class="mb-3">Why is this lead being rejected?</p>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="failure_reason" id="poa" value="Failed:POA" required>
                            <label class="form-check-label" for="poa">
                                <strong>Failed:POA</strong> - Power of Attorney
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="failure_reason" id="dnqAge" value="Failed:DNQ-Age" required>
                            <label class="form-check-label" for="dnqAge">
                                <strong>Failed:DNQ-Age</strong>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="failure_reason" id="declinedSSN" value="Failed:Declined SSN" required>
                            <label class="form-check-label" for="declinedSSN">
                                <strong>Failed:Declined SSN</strong>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="failure_reason" id="notInterested" value="Failed:Not Interested" required>
                            <label class="form-check-label" for="notInterested">
                                <strong>Failed:Not Interested</strong>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="failure_reason" id="dnc" value="Failed:DNC" required>
                            <label class="form-check-label" for="dnc">
                                <strong>Failed:DNC</strong> - Do Not Call
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="failure_reason" id="cannotAfford" value="Failed:Cannot Afford" required>
                            <label class="form-check-label" for="cannotAfford">
                                <strong>Failed:Cannot Afford</strong>
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="failure_reason" id="dnqHealth" value="Failed:DNQ-Health" required>
                            <label class="form-check-label" for="dnqHealth">
                                <strong>Failed:DNQ-Health</strong> - Health Conditions
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="failure_reason" id="declinedBanking" value="Failed:Declined Banking" required>
                            <label class="form-check-label" for="declinedBanking">
                                <strong>Failed:Declined Banking</strong>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Confirm Failed</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
