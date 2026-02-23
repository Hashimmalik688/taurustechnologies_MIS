@extends('layouts.master')

@section('title') Edit Insurance Carrier @endsection

@section('css')
<style>
/* ─── Carrier Edit ─── */
.ce-hdr { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; }
.ce-hdr h5 { font-weight:800; font-size:1rem; color:var(--bs-surface-800); display:flex; align-items:center; gap:.4rem; margin:0; }
.ce-back { font-size:.68rem; font-weight:600; padding:.3rem .7rem; border-radius:.35rem; border:1px solid var(--bs-surface-200); background:var(--bs-card-bg); color:var(--bs-surface-500); text-decoration:none; display:inline-flex; align-items:center; gap:.25rem; transition:all .15s; }
.ce-back:hover { border-color:var(--bs-gradient-start); color:var(--bs-gradient-start); }

.ce-card { background:var(--bs-card-bg); border-radius:.6rem; box-shadow:0 1px 4px rgba(0,0,0,.04); border:1.5px solid var(--bs-surface-100); margin-bottom:.75rem; overflow:hidden; }
.ce-card-hdr { padding:.55rem .8rem; border-bottom:1px solid var(--bs-surface-50); display:flex; align-items:center; gap:.3rem; font-weight:700; font-size:.72rem; color:var(--bs-surface-700); }
.ce-card-hdr i { font-size:.9rem; color:var(--bs-gradient-start); }
.ce-card-body { padding:.75rem .8rem; }

.ce-label { font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:var(--bs-surface-500); margin-bottom:.2rem; }
.ce-input { border:1px solid var(--bs-surface-200); border-radius:.35rem; padding:.35rem .5rem; font-size:.72rem; width:100%; background:var(--bs-card-bg); transition:all .15s; }
.ce-input:focus { outline:none; border-color:var(--bs-gradient-start); box-shadow:0 0 0 2px rgba(102,126,234,.1); }
.ce-select { border:1px solid var(--bs-surface-200); border-radius:.35rem; padding:.35rem .5rem; font-size:.72rem; width:100%; background:var(--bs-card-bg); cursor:pointer; }
.ce-select:focus { outline:none; border-color:var(--bs-gradient-start); }
.ce-hint { font-size:.55rem; color:var(--bs-surface-400); margin-top:.15rem; }
.ce-textarea { border:1px solid var(--bs-surface-200); border-radius:.35rem; padding:.35rem .5rem; font-size:.72rem; width:100%; background:var(--bs-card-bg); resize:vertical; min-height:60px; }
.ce-textarea:focus { outline:none; border-color:var(--bs-gradient-start); box-shadow:0 0 0 2px rgba(102,126,234,.1); }

.ce-switch-wrap { display:flex; align-items:center; gap:.5rem; }
.ce-switch-label { font-size:.68rem; font-weight:600; color:var(--bs-surface-600); }

/* Bracket */
.ce-bracket { background:var(--bs-surface-bg-light); border-radius:.4rem; padding:.5rem .6rem; margin-bottom:.4rem; border-left:3px solid var(--bs-gradient-start); position:relative; }
.ce-bracket .row { align-items:flex-end; }
.ce-bracket .ce-label { margin-bottom:.12rem; }
.ce-bracket-rm { position:absolute; top:.3rem; right:.3rem; border:none; background:transparent; color:var(--bs-surface-400); cursor:pointer; font-size:.8rem; padding:0; line-height:1; }
.ce-bracket-rm:hover { color:#f46a6a; }
.ce-add-bracket { font-size:.62rem; font-weight:600; padding:.25rem .6rem; border-radius:.3rem; border:1px dashed var(--bs-surface-300); background:transparent; color:var(--bs-surface-500); cursor:pointer; display:inline-flex; align-items:center; gap:.2rem; transition:all .15s; }
.ce-add-bracket:hover { border-color:var(--bs-gradient-start); color:var(--bs-gradient-start); }

.ce-formula { background:rgba(102,126,234,.04); border:1px solid rgba(102,126,234,.12); border-radius:.4rem; padding:.4rem .6rem; font-size:.62rem; color:var(--bs-surface-600); display:flex; align-items:center; gap:.3rem; margin-bottom:.6rem; }
.ce-formula i { color:var(--bs-gradient-start); font-size:.8rem; }

.ce-info-bar { background:rgba(102,126,234,.04); border:1px solid rgba(102,126,234,.12); border-radius:.4rem; padding:.35rem .6rem; font-size:.6rem; color:var(--bs-surface-600); display:flex; align-items:center; gap:.25rem; }
.ce-info-bar i { color:var(--bs-gradient-start); }

.ce-actions { display:flex; gap:.4rem; margin-top:.5rem; }
.ce-btn-save { background:linear-gradient(135deg,var(--bs-gradient-start),var(--bs-gradient-end)); color:#fff; border:none; padding:.4rem .9rem; border-radius:.4rem; font-size:.68rem; font-weight:600; display:inline-flex; align-items:center; gap:.25rem; cursor:pointer; transition:all .2s; box-shadow:0 2px 8px rgba(102,126,234,.25); }
.ce-btn-save:hover { transform:translateY(-1px); box-shadow:0 4px 14px rgba(102,126,234,.35); }
.ce-btn-cancel { border:1px solid var(--bs-surface-200); background:var(--bs-card-bg); color:var(--bs-surface-500); padding:.4rem .9rem; border-radius:.4rem; font-size:.68rem; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:.25rem; transition:all .15s; }
.ce-btn-cancel:hover { border-color:var(--bs-surface-400); color:var(--bs-surface-600); }
</style>
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') <a href="{{ route('admin.insurance-carriers.index') }}">Insurance Clusters</a> @endslot
    @slot('title') Edit {{ $insuranceCarrier->name }} @endslot
@endcomponent

<div class="ce-hdr">
    <h5><i class="bx bx-shield-quarter"></i> Edit Carrier</h5>
    <a href="{{ route('admin.insurance-carriers.index') }}" class="ce-back"><i class="bx bx-arrow-back"></i> Back</a>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show py-2 px-3" style="font-size:.7rem;border-radius:.45rem" role="alert">
    <i class="bx bx-error-circle me-1"></i>
    @foreach($errors->all() as $error) {{ $error }} @endforeach
    <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size:.45rem;padding:.7rem"></button>
</div>
@endif

<form action="{{ route('admin.insurance-carriers.update', $insuranceCarrier->id) }}" method="POST" id="carrierForm">
    @csrf
    @method('PUT')

    <div class="row">
        {{-- Left Column --}}
        <div class="col-lg-6">
            {{-- Basic Info --}}
            <div class="ce-card">
                <div class="ce-card-hdr"><i class="bx bx-info-circle"></i> Basic Information</div>
                <div class="ce-card-body">
                    <div class="row g-2 mb-2">
                        <div class="col-7">
                            <div class="ce-label">Carrier Name <span class="text-danger">*</span></div>
                            <input type="text" name="name" class="ce-input @error('name') is-invalid @enderror" value="{{ old('name', $insuranceCarrier->name) }}" required>
                            <div class="ce-hint">e.g., American Amicable, Foresters</div>
                        </div>
                        <div class="col-5">
                            <div class="ce-label">Payment Module <span class="text-danger">*</span></div>
                            <select name="payment_module" class="ce-select @error('payment_module') is-invalid @enderror" required>
                                <option value="on_draft" {{ old('payment_module', $insuranceCarrier->payment_module) == 'on_draft' ? 'selected' : '' }}>On Draft</option>
                                <option value="on_issue" {{ old('payment_module', $insuranceCarrier->payment_module) == 'on_issue' ? 'selected' : '' }}>On Issue</option>
                                <option value="as_earned" {{ old('payment_module', $insuranceCarrier->payment_module) == 'as_earned' ? 'selected' : '' }}>As Earned</option>
                            </select>
                            <div class="ce-hint">How company gets paid</div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="ce-label">Plan Types</div>
                        <input type="text" name="plan_types" class="ce-input @error('plan_types') is-invalid @enderror" value="{{ old('plan_types', is_array($insuranceCarrier->plan_types) ? implode(', ', $insuranceCarrier->plan_types) : $insuranceCarrier->plan_types) }}" placeholder="G.I, Graded, Level, Modified">
                        <div class="ce-hint">Comma-separated list of plan types</div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <div class="ce-label">Base Commission % <small style="text-transform:none;font-weight:400">(fallback)</small></div>
                            <input type="number" step="0.01" min="0" max="100" name="base_commission_percentage" class="ce-input @error('base_commission_percentage') is-invalid @enderror" value="{{ old('base_commission_percentage', $insuranceCarrier->base_commission_percentage) }}">
                            <div class="ce-hint">Used when age doesn't match brackets</div>
                        </div>
                        <div class="col-6 d-flex align-items-end">
                            <div>
                                <div class="ce-switch-wrap">
                                    <input type="hidden" name="is_active" value="0">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $insuranceCarrier->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label ce-switch-label" for="is_active">Active</label>
                                    </div>
                                </div>
                                <div class="ce-hint">Visible in dropdowns when active</div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="ce-label">Calculation Notes</div>
                        <textarea name="calculation_notes" class="ce-textarea @error('calculation_notes') is-invalid @enderror" placeholder="Special rules, exceptions...">{{ old('calculation_notes', $insuranceCarrier->calculation_notes) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="ce-info-bar">
                <i class="bx bx-bar-chart-alt-2"></i>
                This carrier has <strong>&nbsp;{{ $insuranceCarrier->leads()->count() }}&nbsp;</strong> associated lead(s)
            </div>
        </div>

        {{-- Right Column - Brackets --}}
        <div class="col-lg-6">
            <div class="ce-card">
                <div class="ce-card-hdr" style="justify-content:space-between">
                    <span><i class="bx bx-calculator"></i> Age-Based Commission Brackets</span>
                    <button type="button" class="ce-add-bracket" id="addBracket"><i class="bx bx-plus"></i> Add Bracket</button>
                </div>
                <div class="ce-card-body">
                    <div class="ce-formula"><i class="bx bx-info-circle"></i> <strong>Formula:</strong>&nbsp;Monthly Premium × 9 months × Commission %</div>

                    <div id="brackets-container">
                        @foreach($insuranceCarrier->commissionBrackets as $index => $bracket)
                        <div class="ce-bracket" data-index="{{ $index }}">
                            <button type="button" class="ce-bracket-rm remove-bracket"><i class="bx bx-x"></i></button>
                            <input type="hidden" name="brackets[{{ $index }}][id]" value="{{ $bracket->id }}">
                            <div class="row g-2">
                                <div class="col-3">
                                    <div class="ce-label">Min Age</div>
                                    <input type="number" name="brackets[{{ $index }}][age_min]" class="ce-input" value="{{ $bracket->age_min }}" min="0" max="120" required>
                                </div>
                                <div class="col-3">
                                    <div class="ce-label">Max Age</div>
                                    <input type="number" name="brackets[{{ $index }}][age_max]" class="ce-input" value="{{ $bracket->age_max }}" min="0" max="120" required>
                                </div>
                                <div class="col-3">
                                    <div class="ce-label">Commission %</div>
                                    <input type="number" name="brackets[{{ $index }}][commission_percentage]" class="ce-input" value="{{ $bracket->commission_percentage }}" step="0.01" min="0" max="100" required>
                                </div>
                                <div class="col-3">
                                    <div class="ce-label">Notes</div>
                                    <input type="text" name="brackets[{{ $index }}][notes]" class="ce-input" value="{{ $bracket->notes }}" placeholder="Optional">
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($insuranceCarrier->commissionBrackets->isEmpty())
                    <div class="text-center py-3" id="noBracketsMsg">
                        <i class="bx bx-layer-minus" style="font-size:1.5rem;opacity:.15;display:block;margin-bottom:.3rem"></i>
                        <p style="font-size:.65rem;color:var(--bs-surface-400);margin:0">No brackets defined — base commission % will be used</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="ce-actions">
        <button type="submit" class="ce-btn-save"><i class="bx bx-save"></i> Update Carrier</button>
        <a href="{{ route('admin.insurance-carriers.index') }}" class="ce-btn-cancel"><i class="bx bx-x"></i> Cancel</a>
    </div>
</form>
@endsection

@section('script')
<script>
let bracketIndex = {{ $insuranceCarrier->commissionBrackets->count() }};

document.getElementById('addBracket').addEventListener('click', function() {
    const container = document.getElementById('brackets-container');
    const msg = document.getElementById('noBracketsMsg');
    if(msg) msg.remove();

    const div = document.createElement('div');
    div.className = 'ce-bracket';
    div.setAttribute('data-index', bracketIndex);
    div.innerHTML = `
        <button type="button" class="ce-bracket-rm remove-bracket"><i class="bx bx-x"></i></button>
        <div class="row g-2">
            <div class="col-3">
                <div class="ce-label">Min Age</div>
                <input type="number" name="brackets[${bracketIndex}][age_min]" class="ce-input" min="0" max="120" required>
            </div>
            <div class="col-3">
                <div class="ce-label">Max Age</div>
                <input type="number" name="brackets[${bracketIndex}][age_max]" class="ce-input" min="0" max="120" required>
            </div>
            <div class="col-3">
                <div class="ce-label">Commission %</div>
                <input type="number" name="brackets[${bracketIndex}][commission_percentage]" class="ce-input" step="0.01" min="0" max="100" required>
            </div>
            <div class="col-3">
                <div class="ce-label">Notes</div>
                <input type="text" name="brackets[${bracketIndex}][notes]" class="ce-input" placeholder="Optional">
            </div>
        </div>
    `;
    container.appendChild(div);
    bracketIndex++;
});

document.addEventListener('click', function(e) {
    if (e.target && e.target.closest('.remove-bracket')) {
        if (confirm('Remove this bracket?')) {
            e.target.closest('.ce-bracket').remove();
        }
    }
});
</script>
@endsection
