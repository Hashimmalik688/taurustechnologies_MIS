@extends('layouts.master')

@section('title') Edit Partner — {{ $partner->name }} @endsection

@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-5-theme/1.3.0/select2-bootstrap-5-theme.min.css" rel="stylesheet">
<style>
/* ═══════════════════════════════════════════════════
   Edit Partner — Executive Dashboard Theme
   ═══════════════════════════════════════════════════ */

/* Page Header */
.ep-page-hdr { display:flex; justify-content:space-between; align-items:center; margin-bottom:.75rem; flex-wrap:wrap; gap:.5rem; }
.ep-page-hdr h5 { font-weight:800; font-size:1.05rem; color:var(--bs-surface-800); display:flex; align-items:center; gap:.5rem; margin:0; }
.ep-back-btn { font-size:.68rem; font-weight:600; padding:.3rem .7rem; border-radius:.35rem; border:1px solid var(--bs-surface-200); background:var(--bs-card-bg); color:var(--bs-surface-500); text-decoration:none; display:inline-flex; align-items:center; gap:.25rem; transition:all .15s; }
.ep-back-btn:hover { border-color:#556ee6; color:#556ee6; }

/* Card System */
.ep-card {
    background:var(--bs-card-bg); border-radius:.6rem; overflow:hidden;
    box-shadow:0 1px 4px rgba(0,0,0,.05); border:1px solid rgba(0,0,0,.04);
    margin-bottom:.65rem;
}
.ep-card-hdr {
    display:flex; align-items:center; gap:.4rem;
    padding:.5rem .75rem; border-bottom:1px solid rgba(0,0,0,.05);
}
.ep-card-hdr h6 { font-weight:700; font-size:.78rem; color:var(--bs-surface-700); margin:0; }
.ep-card-hdr i { color:#556ee6; font-size:.9rem; }
.ep-card-body { padding:.75rem; }

/* Form Elements */
.ep-label { font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--bs-surface-500); margin-bottom:.2rem; display:block; }
.ep-label.required::after { content:' *'; color:#c84646; font-weight:700; }
.ep-input {
    font-size:.72rem; border:1px solid rgba(0,0,0,.08); border-radius:.35rem;
    padding:.4rem .6rem; width:100%; background:var(--bs-card-bg); transition:all .2s;
}
.ep-input:focus { outline:none; border-color:#556ee6; box-shadow:0 0 0 2px rgba(85,110,230,.1); }
.ep-hint { font-size:.55rem; color:var(--bs-surface-400); margin-top:.15rem; }

/* Status box */
.ep-status-box { display:flex; align-items:center; gap:.4rem; padding:.35rem .6rem; border-radius:.35rem; font-size:.65rem; font-weight:600; }
.ep-status-box.set { background:rgba(52,195,143,.08); color:#1a8754; border:1px solid rgba(52,195,143,.12); }
.ep-status-box.unset { background:rgba(244,106,106,.08); color:#c84646; border:1px solid rgba(244,106,106,.12); }

/* Switch */
.ep-switch { display:flex; align-items:center; gap:.5rem; }
.ep-switch label { font-size:.72rem; font-weight:600; color:var(--bs-surface-600); cursor:pointer; }

/* Carrier Section */
.ep-carrier-sec {
    border:1px solid rgba(0,0,0,.06); border-radius:.5rem; padding:.75rem;
    margin-bottom:.5rem; background:rgba(0,0,0,.01); transition:all .2s;
}
.ep-carrier-sec:hover { border-color:rgba(85,110,230,.2); }
.ep-carrier-hdr { display:flex; justify-content:space-between; align-items:center; margin-bottom:.65rem; }
.ep-carrier-name { font-weight:700; font-size:.78rem; color:#556ee6; display:flex; align-items:center; gap:.35rem; }
.ep-carrier-remove { font-size:.6rem; color:#f46a6a; cursor:pointer; display:inline-flex; align-items:center; gap:.2rem; border:1px solid rgba(244,106,106,.15); padding:.15rem .4rem; border-radius:.3rem; background:rgba(244,106,106,.04); transition:all .15s; }
.ep-carrier-remove:hover { background:rgba(244,106,106,.1); border-color:#f46a6a; }

.ep-commission-card { background:var(--bs-card-bg); border:1px solid rgba(0,0,0,.04); border-radius:.45rem; padding:.65rem; margin-top:.4rem; }
.ep-commission-card h6 { font-size:.65rem; font-weight:700; color:var(--bs-surface-600); margin-bottom:.4rem; text-transform:uppercase; letter-spacing:.3px; }

/* State tags — white-theme-safe */
.ep-state-tags { display:flex; flex-wrap:wrap; gap:.25rem; margin-top:.4rem; }
.ep-state-tag { font-size:.55rem; font-weight:700; padding:.1rem .35rem; border-radius:.2rem; background:rgba(85,110,230,.1); color:#556ee6; border:1px solid rgba(85,110,230,.12); }

/* Info bar */
.ep-info-bar {
    font-size:.65rem; color:#556ee6; padding:.35rem .6rem; border-radius:.3rem;
    background:rgba(85,110,230,.06); border:1px solid rgba(85,110,230,.08);
    border-left:3px solid #556ee6; margin-bottom:.65rem;
}
.ep-info-bar i { font-size:.8rem; }

/* Toolbar */
.ep-toolbar-btns { display:flex; gap:.35rem; margin-left:auto; }
.ep-toolbar-btn { font-size:.6rem; padding:.2rem .5rem; border-radius:.3rem; border:1px solid rgba(0,0,0,.06); background:var(--bs-card-bg); color:var(--bs-surface-500); cursor:pointer; transition:all .15s; display:inline-flex; align-items:center; gap:.2rem; }
.ep-toolbar-btn:hover { border-color:#556ee6; color:#556ee6; }
.ep-toolbar-btn.success { border-color:rgba(52,195,143,.2); color:#1a8754; }
.ep-toolbar-btn.success:hover { background:rgba(52,195,143,.05); }

/* Submit */
.ep-actions { display:flex; justify-content:flex-end; gap:.4rem; margin-top:.75rem; }
.ep-btn {
    font-size:.68rem; font-weight:600; padding:.4rem .9rem;
    border-radius:.4rem; border:none; cursor:pointer; transition:all .2s;
    display:inline-flex; align-items:center; gap:.25rem;
}
.ep-btn.primary {
    background:linear-gradient(135deg,var(--bs-gradient-start),var(--bs-gradient-end));
    color:#fff; box-shadow:0 2px 8px rgba(102,126,234,.25);
}
.ep-btn.primary:hover { transform:translateY(-1px); box-shadow:0 4px 14px rgba(102,126,234,.35); }
.ep-btn.secondary { background:var(--bs-card-bg); border:1px solid var(--bs-surface-200); color:var(--bs-surface-600); text-decoration:none; }
.ep-btn.secondary:hover { border-color:var(--bs-surface-400); color:var(--bs-surface-700); }

/* Alert */
.ep-alert {
    border-radius:.4rem; padding:.45rem .65rem; font-size:.72rem; margin-bottom:.65rem;
    border:none; border-left:3px solid;
}
.ep-alert.success { background:rgba(52,195,143,.08); border-left-color:#1a8754; color:#1a8754; }
.ep-alert.danger { background:rgba(244,106,106,.08); border-left-color:#f46a6a; color:#c84646; }

/* Select2 overrides */
.select2-container { width:100% !important; }
.select2-selection { border:1px solid rgba(0,0,0,.08) !important; border-radius:.35rem !important; min-height:34px !important; font-size:.72rem !important; }
.select2-selection--multiple .select2-selection__choice { background:linear-gradient(135deg,#556ee6,#764ba2) !important; border:none !important; color:#fff !important; border-radius:.25rem !important; font-size:.58rem !important; padding:.1rem .3rem !important; }
.select2-container--focus .select2-selection { border-color:#556ee6 !important; box-shadow:0 0 0 2px rgba(85,110,230,.1) !important; }
</style>
@endsection

@section('content')

<!-- Page Header -->
<div class="ep-page-hdr">
    <h5><i class="bx bx-edit" style="color:#556ee6"></i> Edit Partner — {{ $partner->name }}</h5>
    <a href="{{ route('admin.partners.index') }}" class="ep-back-btn"><i class="bx bx-arrow-back"></i> Back to Partners</a>
</div>

@if(session('success'))
<div class="ep-alert success">
    <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="ep-alert danger">
    <i class="bx bx-error me-1"></i> {{ session('error') }}
</div>
@endif

<form method="POST" action="{{ route('admin.partners.update', $partner->id) }}" id="partnerForm">
    @csrf @method('PUT')

    <!-- Basic Information -->
    <div class="ep-card">
        <div class="ep-card-hdr"><i class="bx bx-user"></i><h6>Basic Information</h6></div>
        <div class="ep-card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="ep-label required">Partner Name</label>
                    <input type="text" class="ep-input @error('name') is-invalid @enderror" name="name" value="{{ old('name', $partner->name) }}" required>
                    @error('name')<div class="invalid-feedback" style="font-size:.62rem">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="ep-label required">Partner Code</label>
                    <input type="text" class="ep-input @error('code') is-invalid @enderror" name="code" value="{{ old('code', $partner->code) }}" required>
                    @error('code')<div class="invalid-feedback" style="font-size:.62rem">{{ $message }}</div>@enderror
                    <div class="ep-hint">Unique ID e.g., E-1, Y-1, F-1</div>
                </div>
                <div class="col-md-3">
                    <label class="ep-label required">Type</label>
                    <select class="ep-input @error('type') is-invalid @enderror" name="type" required>
                        <option value="partner" {{ old('type', $partner->type) === 'partner' ? 'selected' : '' }}>Partner</option>
                        <option value="agent" {{ old('type', $partner->type) === 'agent' ? 'selected' : '' }}>Downline</option>
                    </select>
                    @error('type')<div class="invalid-feedback" style="font-size:.62rem">{{ $message }}</div>@enderror
                    <div class="ep-hint">Partner = standalone/upline. Downline = works under a partner</div>
                </div>
                <div class="col-md-3">
                    <label class="ep-label">Upline Partner</label>
                    <select class="ep-input @error('parent_partner_id') is-invalid @enderror" name="parent_partner_id">
                        <option value="">— None (standalone) —</option>
                        @foreach(\App\Models\Partner::partners()->where('id', '!=', $partner->id)->orderBy('name')->get() as $pp)
                            <option value="{{ $pp->id }}" {{ old('parent_partner_id', $partner->parent_partner_id) == $pp->id ? 'selected' : '' }}>{{ $pp->name }} ({{ $pp->code }})</option>
                        @endforeach
                    </select>
                    @error('parent_partner_id')<div class="invalid-feedback" style="font-size:.62rem">{{ $message }}</div>@enderror
                    <div class="ep-hint">Required for downline — their upline partner</div>
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
                        {{ $partner->password ? 'Password set — partner can login' : 'No password — partner cannot login' }}
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="ep-label">New Password</label>
                    <input type="password" class="ep-input @error('password') is-invalid @enderror" id="pw_new" name="password" placeholder="Min 8 characters">
                    @error('password')<div class="invalid-feedback" style="font-size:.62rem">{{ $message }}</div>@enderror
                    <div class="ep-hint">Leave blank to keep current password</div>
                </div>
                <div class="col-md-4">
                    <label class="ep-label">Confirm Password</label>
                    <input type="password" class="ep-input" id="pw_confirm" name="password_confirmation" placeholder="Confirm new password" oninput="checkPwMatch()">
                    <div id="pw_match_msg" style="font-size:.62rem;margin-top:.15rem;display:none"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Carriers & States -->
    <div class="ep-card">
        <div class="ep-card-hdr">
            <i class="bx bx-briefcase"></i><h6>Carriers & Licensed States</h6>
            <div class="ep-toolbar-btns">
                <button type="button" class="ep-toolbar-btn" onclick="toggleAllCarriers()"><i class="bx bx-show"></i> Show/Hide</button>
                <button type="button" class="ep-toolbar-btn success" onclick="openCreateCarrierModal()"><i class="bx bx-plus"></i> New Carrier</button>
            </div>
        </div>
        <div class="ep-card-body">
            <div class="ep-info-bar">
                <i class="bx bx-info-circle me-1"></i> Select states for each carrier. Commission rates apply to ALL selected states per carrier.
            </div>

            @include('admin.partners.partials.carrier-states', [
                'insuranceCarriers' => $insuranceCarriers,
                'partnerCarrierStates' => $partnerCarrierStates ?? collect()
            ])
        </div>
    </div>

    <!-- Actions -->
    <div class="ep-actions">
        <a href="{{ route('admin.partners.index') }}" class="ep-btn secondary"><i class="bx bx-x"></i> Cancel</a>
        <button type="submit" class="ep-btn primary"><i class="bx bx-save"></i> Update Partner</button>
    </div>
</form>
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
<script>
function checkPwMatch() {
    const pw = document.getElementById('pw_new').value;
    const pc = document.getElementById('pw_confirm').value;
    const msg = document.getElementById('pw_match_msg');
    if (!pw && !pc) { msg.style.display='none'; return; }
    if (pw === pc) {
        msg.style.display='block'; msg.style.color='#1a8754'; msg.textContent='✓ Passwords match';
        document.getElementById('pw_confirm').style.borderColor='#34c38f';
    } else {
        msg.style.display='block'; msg.style.color='#c84646'; msg.textContent='✗ Passwords do not match';
        document.getElementById('pw_confirm').style.borderColor='#f46a6a';
    }
}

$(document).ready(function() {
    // All carrier sections are visible — initialize select2 on all of them now
    $('.select2-multiple').select2({ placeholder:"Select states...", allowClear:true, width:'100%', theme:'bootstrap-5' });

    // Before submit: validate password match, then sync select2 values
    $('form').on('submit', function(e) {
        // Block submit if password filled but confirm doesn't match
        const pw = $('input[name=password]').val();
        const pc = $('input[name=password_confirmation]').val();
        if (pw && pw !== pc) {
            e.preventDefault();
            alert('Password and confirm password do not match. Please fix before saving, or clear both fields to keep the current password.');
            $('input[name=password_confirmation]').focus();
            return false;
        }
        // Sync select2 values to underlying <select> before POST
        $('.select2-multiple').each(function() {
            const vals = $(this).val() || [];
            $(this).find('option').prop('selected', false);
            vals.forEach(v => { $(this).find('option[value="' + v + '"]').prop('selected', true); });
        });
    });
});

function updateStateSettlementFields(carrierId) {
    const sel = document.getElementById('carrier_states_' + carrierId);
    const states = Array.from(sel.selectedOptions).map(o => o.value);
    const div = document.getElementById('selected-states-' + carrierId);
    if (div && states.length > 0) {
        div.innerHTML = '<div class="cs-state-tags">' + states.map(s => '<span class="cs-state-pill">' + s + '</span>').join('') + '</div>';
    } else if (div) { div.innerHTML = ''; }
}

function toggleAllCarriers() {
    document.querySelectorAll('.cs-carrier').forEach(s => s.classList.toggle('d-none'));
}

function removeCarrierSection(carrierId) {
    if (!confirm('Remove this carrier? All state assignments will be cleared.')) return;
    const sec = document.getElementById('carrier-state-section-' + carrierId);
    const sel = document.getElementById('carrier_states_' + carrierId);
    if (sel && $(sel).hasClass('select2-hidden-accessible')) { $(sel).select2('destroy'); }
    sec.querySelectorAll('input[name^="settlement_"]').forEach(i => i.remove());
    if (sel) sel.remove();
    sec.style.display = 'none';
}

function openCreateCarrierModal() {
    const w=900, h=700, l=(screen.width-w)/2, t=(screen.height-h)/2;
    window.open('{{ route("admin.insurance-carriers.create") }}?modal=1', 'CreateCarrier', `width=${w},height=${h},left=${l},top=${t},scrollbars=yes,resizable=yes`);
    window.addEventListener('message', function(e) { if (e.data.type === 'carrierCreated') location.reload(); });
}

function copyStatesFrom(targetCarrierId) {
    // Collect carrier names and IDs from the page
    const allSections = document.querySelectorAll('.cs-carrier');
    const carrierOpts = [];
    allSections.forEach(sec => {
        const nameEl = sec.querySelector('.cs-carrier-name');
        const cleansed = nameEl ? nameEl.textContent.replace(/\s+/g, ' ').trim() : '';
        const idMatch = sec.id.match(/carrier-state-section-(\d+)/);
        if (idMatch && parseInt(idMatch[1]) !== targetCarrierId) {
            carrierOpts.push({ id: parseInt(idMatch[1]), name: cleansed });
        }
    });
    if (carrierOpts.length === 0) {
        alert('No other carriers to copy states from.');
        return;
    }
    // Build a selector prompt
    let list = '';
    carrierOpts.forEach((c, i) => { list += `${i+1}. ${c.name}\n`; });
    const choice = prompt(`Copy states & commission rates FROM:\n\n${list}\nType the number or carrier name:`, '');
    if (!choice) return;
    // Match by number or name
    let source = null;
    if (/^\d+$/.test(choice)) {
        const idx = parseInt(choice) - 1;
        if (idx >= 0 && idx < carrierOpts.length) source = carrierOpts[idx];
    } else {
        source = carrierOpts.find(c => c.name.toLowerCase().includes(choice.toLowerCase()));
    }
    if (!source) { alert('No matching carrier found.'); return; }

    // Copy states from source to target select2
    const sourceSelect = document.getElementById('carrier_states_' + source.id);
    const targetSelect = document.getElementById('carrier_states_' + targetCarrierId);
    if (!sourceSelect || !targetSelect) { alert('Source or target carrier not found on page.'); return; }
    const selectedValues = $(sourceSelect).val() || [];
    if (selectedValues.length === 0) { alert('Source carrier has no states selected.'); return; }

    $(targetSelect).val(selectedValues).trigger('change');

    // Copy commission rates from source to target
    const rateFields = ['level','graded','gi','modified'];
    rateFields.forEach(field => {
        // Look for the input: name="settlement_LEVEL[SOURCE_ID]"
        const sourceInput = document.querySelector(`input[name^="settlement_${field}"][name$="[${source.id}]"]`);
        const targetInput = document.querySelector(`input[name^="settlement_${field}"][name$="[${targetCarrierId}]"]`);
        if (sourceInput && targetInput) {
            targetInput.value = sourceInput.value;
        }
    });

    updateStateSettlementFields(targetCarrierId);
}
</script>
@endsection
