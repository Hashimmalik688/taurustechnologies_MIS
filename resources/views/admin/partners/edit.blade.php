@extends('layouts.master')

@section('title') Edit Partner — {{ $partner->name }} @endsection

@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-5-theme/1.3.0/select2-bootstrap-5-theme.min.css" rel="stylesheet">
<style>
/* ─── Edit Partner Page ─── */
.ep-page-hdr { display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; }
.ep-page-hdr h5 { font-weight:800; font-size:1.05rem; color:var(--bs-surface-800); display:flex; align-items:center; gap:.5rem; margin:0; }
.ep-back-btn { font-size:.72rem; color:var(--bs-surface-500); text-decoration:none; display:inline-flex; align-items:center; gap:.25rem; transition:all .15s; }
.ep-back-btn:hover { color:var(--bs-gradient-start); }

.ep-card { background:var(--bs-card-bg); border-radius:.75rem; box-shadow:0 1px 4px rgba(0,0,0,.04); overflow:hidden; margin-bottom:1rem; }
.ep-card-hdr { padding:.65rem 1rem; border-bottom:1px solid var(--bs-surface-100); display:flex; align-items:center; gap:.5rem; }
.ep-card-hdr h6 { font-weight:700; font-size:.78rem; color:var(--bs-surface-700); margin:0; }
.ep-card-hdr i { color:var(--bs-gradient-start); font-size:.9rem; }
.ep-card-body { padding:1rem; }

.ep-label { font-size:.68rem; font-weight:600; color:var(--bs-surface-600); margin-bottom:.3rem; display:block; }
.ep-label.required::after { content:' *'; color:#f46a6a; }
.ep-input { font-size:.72rem; border:1.5px solid var(--bs-surface-200); border-radius:.4rem; padding:.4rem .6rem; width:100%; transition:all .2s; background:var(--bs-card-bg); }
.ep-input:focus { outline:none; border-color:var(--bs-gradient-start); box-shadow:0 0 0 2px rgba(102,126,234,.1); }
.ep-hint { font-size:.6rem; color:var(--bs-surface-400); margin-top:.2rem; }

.ep-status-box { display:flex; align-items:center; gap:.5rem; padding:.4rem .6rem; border-radius:.4rem; font-size:.68rem; font-weight:600; }
.ep-status-box.set { background:rgba(52,195,143,.08); color:#34c38f; }
.ep-status-box.unset { background:rgba(244,106,106,.08); color:#f46a6a; }

/* Carrier Section */
.ep-carrier-sec { border:1.5px solid var(--bs-surface-200); border-radius:.6rem; padding:1rem; margin-bottom:.75rem; background:var(--bs-surface-bg-light); transition:all .2s; }
.ep-carrier-sec:hover { border-color:var(--bs-gradient-start); }
.ep-carrier-hdr { display:flex; justify-content:space-between; align-items:center; margin-bottom:.65rem; }
.ep-carrier-name { font-weight:700; font-size:.78rem; color:var(--bs-gradient-start); display:flex; align-items:center; gap:.35rem; }
.ep-carrier-remove { font-size:.62rem; color:#f46a6a; cursor:pointer; display:inline-flex; align-items:center; gap:.2rem; border:1px solid rgba(244,106,106,.2); padding:.15rem .4rem; border-radius:.3rem; background:rgba(244,106,106,.04); transition:all .15s; }
.ep-carrier-remove:hover { background:rgba(244,106,106,.1); border-color:#f46a6a; }

.ep-commission-card { background:var(--bs-card-bg); border:1px solid var(--bs-surface-200); border-radius:.5rem; padding:.75rem; margin-top:.5rem; }
.ep-commission-card h6 { font-size:.68rem; font-weight:700; color:var(--bs-surface-600); margin-bottom:.5rem; }

.ep-state-tags { display:flex; flex-wrap:wrap; gap:.25rem; margin-top:.4rem; }
.ep-state-tag { font-size:.55rem; font-weight:600; padding:.1rem .35rem; border-radius:.2rem; background:rgba(102,126,234,.1); color:var(--bs-gradient-start); }

/* Submit */
.ep-submit-row { display:flex; justify-content:flex-end; gap:.5rem; margin-top:1rem; }
.ep-btn { font-size:.72rem; font-weight:600; padding:.45rem 1.25rem; border-radius:.45rem; border:none; cursor:pointer; transition:all .2s; display:inline-flex; align-items:center; gap:.3rem; }
.ep-btn.primary { background:linear-gradient(135deg,var(--bs-gradient-start),var(--bs-gradient-end)); color:#fff; box-shadow:0 2px 8px rgba(102,126,234,.25); }
.ep-btn.primary:hover { transform:translateY(-1px); box-shadow:0 4px 12px rgba(102,126,234,.35); }
.ep-btn.secondary { background:var(--bs-surface-200); color:var(--bs-surface-600); text-decoration:none; }
.ep-btn.secondary:hover { background:var(--bs-surface-300); color:var(--bs-surface-700); }

/* Switch */
.ep-switch { display:flex; align-items:center; gap:.5rem; }
.ep-switch label { font-size:.72rem; font-weight:600; color:var(--bs-surface-600); cursor:pointer; }

/* Select2 overrides */
.select2-container { width:100% !important; }
.select2-selection { border:1.5px solid var(--bs-surface-200) !important; border-radius:.4rem !important; min-height:34px !important; font-size:.72rem !important; }
.select2-selection--multiple .select2-selection__choice { background:linear-gradient(135deg,var(--bs-gradient-start),var(--bs-gradient-end)) !important; border:none !important; color:#fff !important; border-radius:.25rem !important; font-size:.6rem !important; padding:.1rem .35rem !important; }
.select2-container--focus .select2-selection { border-color:var(--bs-gradient-start) !important; box-shadow:0 0 0 2px rgba(102,126,234,.1) !important; }
.ep-toolbar-btns { display:flex; gap:.4rem; }
.ep-toolbar-btn { font-size:.65rem; padding:.25rem .6rem; border-radius:.35rem; border:1px solid var(--bs-surface-200); background:var(--bs-card-bg); color:var(--bs-surface-600); cursor:pointer; transition:all .15s; display:inline-flex; align-items:center; gap:.25rem; }
.ep-toolbar-btn:hover { border-color:var(--bs-gradient-start); color:var(--bs-gradient-start); }
.ep-toolbar-btn.success { border-color:#34c38f; color:#34c38f; }
.ep-toolbar-btn.success:hover { background:rgba(52,195,143,.05); }
</style>
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') <a href="{{ route('admin.partners.index') }}">Partners</a> @endslot
    @slot('title') Edit Partner @endslot
@endcomponent

<div class="ep-page-hdr">
    <h5><i class="bx bx-edit"></i> Edit Partner — {{ $partner->name }}</h5>
    <a href="{{ route('admin.partners.index') }}" class="ep-back-btn"><i class="bx bx-arrow-back"></i> Back to Partners</a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show py-2 px-3" style="font-size:.75rem;border-radius:.5rem" role="alert">
    <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size:.5rem;padding:.75rem"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show py-2 px-3" style="font-size:.75rem;border-radius:.5rem" role="alert">
    <i class="bx bx-error me-1"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size:.5rem;padding:.75rem"></button>
</div>
@endif

<form method="POST" action="{{ route('admin.partners.update', $partner->id) }}" id="partnerForm">
    @csrf @method('PUT')

    <!-- Basic Information -->
    <div class="ep-card">
        <div class="ep-card-hdr"><i class="bx bx-user"></i><h6>Basic Information</h6></div>
        <div class="ep-card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="ep-label required">Partner Name</label>
                    <input type="text" class="ep-input @error('name') is-invalid @enderror" name="name" value="{{ old('name', $partner->name) }}" required>
                    @error('name')<div class="invalid-feedback" style="font-size:.62rem">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="ep-label required">Partner Code</label>
                    <input type="text" class="ep-input @error('code') is-invalid @enderror" name="code" value="{{ old('code', $partner->code) }}" required>
                    @error('code')<div class="invalid-feedback" style="font-size:.62rem">{{ $message }}</div>@enderror
                    <div class="ep-hint">Unique ID e.g., E-1, Y-1, F-1</div>
                </div>
                <div class="col-md-4">
                    <label class="ep-label">Email Address</label>
                    <input type="email" class="ep-input @error('email') is-invalid @enderror" name="email" value="{{ old('email', $partner->email) }}">
                    @error('email')<div class="invalid-feedback" style="font-size:.62rem">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="ep-label">Phone Number</label>
                    <input type="text" class="ep-input @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $partner->phone) }}" placeholder="(555) 123-4567">
                    @error('phone')<div class="invalid-feedback" style="font-size:.62rem">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="ep-label">Last 4 of SSN</label>
                    <input type="text" class="ep-input @error('ssn_last4') is-invalid @enderror" name="ssn_last4" value="{{ old('ssn_last4', $partner->ssn_last4) }}" maxlength="4" pattern="[0-9]{4}" placeholder="1234">
                    @error('ssn_last4')<div class="invalid-feedback" style="font-size:.62rem">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="ep-label">Our Commission %</label>
                    <input type="number" class="ep-input @error('our_commission_percentage') is-invalid @enderror" name="our_commission_percentage" value="{{ old('our_commission_percentage', $partner->our_commission_percentage ?? 0) }}" min="0" max="100" step="0.01">
                    @error('our_commission_percentage')<div class="invalid-feedback" style="font-size:.62rem">{{ $message }}</div>@enderror
                    <div class="ep-hint">% of total revenue partner owes us</div>
                </div>
                <div class="col-md-3">
                    <label class="ep-label">Status</label>
                    <div class="ep-switch" style="margin-top:.3rem">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $partner->is_active) ? 'checked' : '' }}>
                        <label for="is_active">Active Partner</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Password Management -->
    <div class="ep-card">
        <div class="ep-card-hdr"><i class="bx bx-lock-alt"></i><h6>Password Management</h6></div>
        <div class="ep-card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="ep-label">Current Status</label>
                    <div class="ep-status-box {{ $partner->password ? 'set' : 'unset' }}">
                        <i class="bx {{ $partner->password ? 'bx-check-circle' : 'bx-error-circle' }}"></i>
                        {{ $partner->password ? 'Password is set — partner can login' : 'No password set — partner cannot login' }}
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="ep-label">New Password</label>
                    <input type="password" class="ep-input @error('password') is-invalid @enderror" name="password" placeholder="Min 8 characters">
                    @error('password')<div class="invalid-feedback" style="font-size:.62rem">{{ $message }}</div>@enderror
                    <div class="ep-hint">Leave blank to keep current</div>
                </div>
                <div class="col-md-4">
                    <label class="ep-label">Confirm Password</label>
                    <input type="password" class="ep-input" name="password_confirmation" placeholder="Confirm new password">
                </div>
            </div>
        </div>
    </div>

    <!-- Carriers & States -->
    <div class="ep-card">
        <div class="ep-card-hdr">
            <i class="bx bx-briefcase"></i><h6>Carriers & Licensed States</h6>
            <div class="ms-auto ep-toolbar-btns">
                <button type="button" class="ep-toolbar-btn" onclick="toggleAllCarriers()"><i class="bx bx-show"></i> Show/Hide All</button>
                <button type="button" class="ep-toolbar-btn success" onclick="openCreateCarrierModal()"><i class="bx bx-plus"></i> New Carrier</button>
            </div>
        </div>
        <div class="ep-card-body">
            <div style="font-size:.68rem;color:var(--bs-surface-500);margin-bottom:.75rem;padding:.4rem .6rem;background:rgba(102,126,234,.04);border-radius:.35rem;border-left:3px solid var(--bs-gradient-start);">
                <i class="bx bx-info-circle me-1"></i> Select states for each carrier. Commission rates apply to ALL selected states per carrier.
            </div>

            @include('admin.partners.partials.carrier-states', [
                'insuranceCarriers' => $insuranceCarriers,
                'partnerCarrierStates' => $partnerCarrierStates ?? collect()
            ])
        </div>
    </div>

    <!-- Submit -->
    <div class="ep-submit-row">
        <a href="{{ route('admin.partners.index') }}" class="ep-btn secondary"><i class="bx bx-x"></i> Cancel</a>
        <button type="submit" class="ep-btn primary"><i class="bx bx-save"></i> Update Partner</button>
    </div>
</form>
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2-multiple').select2({ placeholder:"Select states...", allowClear:true, width:'100%', theme:'bootstrap-5' });
    @foreach($insuranceCarriers as $carrier)
        @if(isset($partnerCarrierStates[$carrier->id]) && $partnerCarrierStates[$carrier->id]->isNotEmpty())
            $('#carrier-state-section-{{ $carrier->id }}').removeClass('d-none');
        @endif
    @endforeach
});

function updateStateSettlementFields(carrierId) {
    const sel = document.getElementById('carrier_states_' + carrierId);
    const states = Array.from(sel.selectedOptions).map(o => o.value);
    const div = document.getElementById('selected-states-' + carrierId);
    const sec = document.getElementById('carrier-state-section-' + carrierId);
    if (div && states.length > 0) {
        div.innerHTML = '<div class="ep-state-tags">' + states.map(s => '<span class="ep-state-tag">' + s + '</span>').join('') + '</div>';
    } else if (div) { div.innerHTML = ''; }
    if (states.length > 0) { sec.classList.remove('d-none'); } else { sec.classList.add('d-none'); }
}

function toggleAllCarriers() {
    document.querySelectorAll('.ep-carrier-sec').forEach(s => s.classList.toggle('d-none'));
}

function removeCarrierSection(carrierId) {
    if (!confirm('Remove this carrier? All state assignments will be cleared.')) return;
    const sec = document.getElementById('carrier-state-section-' + carrierId);
    const sel = document.getElementById('carrier_states_' + carrierId);
    sec.querySelectorAll('input[name^="settlement_"]').forEach(i => i.remove());
    if (sel) sel.remove();
    sec.style.display = 'none';
}

function openCreateCarrierModal() {
    const w=900, h=700, l=(screen.width-w)/2, t=(screen.height-h)/2;
    window.open('{{ route("admin.insurance-carriers.create") }}?modal=1', 'CreateCarrier', `width=${w},height=${h},left=${l},top=${t},scrollbars=yes,resizable=yes`);
    window.addEventListener('message', function(e) { if (e.data.type === 'carrierCreated') location.reload(); });
}
</script>
@endsection
