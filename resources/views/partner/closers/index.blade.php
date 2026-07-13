@extends('layouts.partner')

@section('title') Closers @endsection

@section('css')
<style>
    .pc-hdr h4{font-size:1.25rem;font-weight:900;color:#111827;margin:0 0 .25rem;}
    .pc-hdr p{font-size:.84rem;color:#6b7280;margin:0 0 1.25rem;}
    .pc-layout{display:grid;grid-template-columns:340px 1fr;gap:1.25rem;align-items:start;}
    @media(max-width:820px){.pc-layout{grid-template-columns:1fr;}}
    .pc-card{background:#fff;border:1px solid rgba(0,0,0,.08);border-radius:.6rem;box-shadow:0 1px 3px rgba(0,0,0,.06);padding:1.2rem 1.3rem;}
    .pc-card h6{font-size:.72rem;font-weight:800;letter-spacing:.6px;text-transform:uppercase;color:#4f46e5;margin:0 0 1rem;}
    .pc-field{margin-bottom:.85rem;}
    .pc-field label{display:block;font-size:.72rem;font-weight:700;color:#374151;margin-bottom:.3rem;}
    .pc-field label .req{color:#dc2626;}
    .pc-field input{width:100%;border:1px solid rgba(0,0,0,.14);border-radius:.4rem;padding:.45rem .6rem;font-size:.85rem;}
    .pc-field input:focus{outline:none;border-color:#4f46e5;box-shadow:0 0 0 2px rgba(79,70,229,.12);}
    .pc-btn{width:100%;background:#4f46e5;color:#fff;border:none;border-radius:.4rem;padding:.6rem;font-size:.85rem;font-weight:700;cursor:pointer;}
    .pc-btn:hover{background:#4338ca;}
    .pc-table-wrap{background:#fff;border:1px solid rgba(0,0,0,.08);border-radius:.6rem;overflow-x:auto;box-shadow:0 1px 3px rgba(0,0,0,.06);}
    table.pc{width:100%;border-collapse:collapse;font-size:.84rem;}
    table.pc th{text-align:left;padding:.65rem .85rem;background:#f8fafc;font-size:.68rem;font-weight:800;letter-spacing:.5px;text-transform:uppercase;color:#6b7280;border-bottom:1px solid rgba(0,0,0,.07);}
    table.pc td{padding:.6rem .85rem;border-bottom:1px solid rgba(0,0,0,.05);color:#374151;vertical-align:middle;}
    .pc-status{display:inline-block;padding:.2rem .55rem;border-radius:1rem;font-size:.68rem;font-weight:800;}
    .pc-on{background:#ecfdf5;color:#047857;}
    .pc-off{background:#fef2f2;color:#b91c1c;}
    .pc-actions{display:flex;gap:.4rem;flex-wrap:wrap;}
    .pc-mini{border:1px solid rgba(0,0,0,.15);background:#fff;border-radius:.35rem;padding:.28rem .6rem;font-size:.74rem;font-weight:700;cursor:pointer;color:#374151;}
    .pc-mini:hover{background:#f3f4f6;}
    .pc-alert-ok{background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;border-radius:.5rem;padding:.7rem 1rem;font-size:.82rem;margin-bottom:1rem;}
    .pc-alert-err{background:#fef2f2;border:1px solid #fecaca;color:#991b1b;border-radius:.5rem;padding:.7rem 1rem;font-size:.82rem;margin-bottom:1rem;}
    .pc-alert-err ul{margin:.4rem 0 0;padding-left:1.1rem;}
    .pc-empty{padding:2rem;text-align:center;color:#9ca3af;}
</style>
@endsection

@section('content')
<div class="pc-hdr">
    <h4>Closers</h4>
    <p>Create and manage logins for your PJC and Peregrine closers. Each closer signs in and submits sales that flow into the Taurus pipeline under {{ $company->name }}.</p>
</div>

@if(session('success'))<div class="pc-alert-ok">{{ session('success') }}</div>@endif
@if($errors->any())
    <div class="pc-alert-err">Please correct the following:
        <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<div class="pc-layout">
    <div class="pc-card">
        <h6>Add Closer</h6>
        <form action="{{ route('partner.closers.store') }}" method="POST">
            @csrf
            <div class="pc-field"><label>Full Name <span class="req">*</span></label><input type="text" name="name" value="{{ old('name') }}" required></div>
            <div class="pc-field"><label>Email <span class="req">*</span></label><input type="email" name="email" value="{{ old('email') }}" required></div>
            <div class="pc-field"><label>Phone</label><input type="text" name="phone" value="{{ old('phone') }}"></div>
            <div class="pc-field"><label>Password <span class="req">*</span></label><input type="text" name="password" placeholder="min 8 characters" required></div>
            <button type="submit" class="pc-btn"><i class="bx bx-user-plus"></i> Create Closer</button>
        </form>
    </div>

    <div class="pc-table-wrap">
        <table class="pc">
            <thead>
                <tr><th>Name</th><th>Email</th><th>Code</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($closers as $closer)
                    <tr>
                        <td>{{ $closer->name }}</td>
                        <td>{{ $closer->email }}</td>
                        <td>{{ $closer->code }}</td>
                        <td>
                            <span class="pc-status {{ $closer->is_active ? 'pc-on' : 'pc-off' }}">
                                {{ $closer->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="pc-actions">
                                <form action="{{ route('partner.closers.toggle', $closer->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button class="pc-mini" type="submit">{{ $closer->is_active ? 'Deactivate' : 'Activate' }}</button>
                                </form>
                                <form action="{{ route('partner.closers.reset-password', $closer->id) }}" method="POST"
                                      onsubmit="return promptReset(this);">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="password" value="">
                                    <button class="pc-mini" type="submit">Reset Password</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="pc-empty">No closers yet. Add your first closer on the left.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('script')
<script>
    function promptReset(form) {
        var pw = window.prompt('Enter a new password (min 8 characters) for this closer:');
        if (!pw) return false;
        if (pw.length < 8) { alert('Password must be at least 8 characters.'); return false; }
        form.querySelector('input[name="password"]').value = pw;
        return true;
    }
</script>
@endsection
