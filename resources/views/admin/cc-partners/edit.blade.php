@extends('layouts.master')

@section('title') Edit CC Partner @endsection

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h5 class="mb-1 fw-bold"><i class="bx bx-buildings me-1"></i> Edit CC Partner</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">{{ $ccPartner->name }} — {{ $ccPartner->closers()->count() }} closer(s).</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.cc-partners.update', $ccPartner->id) }}" method="POST" style="max-width:640px;">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Company Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $ccPartner->name) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control" value="{{ old('code', $ccPartner->code) }}" maxlength="10" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Login Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $ccPartner->email) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $ccPartner->phone) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">New Password</label>
                        <input type="text" name="password" class="form-control" placeholder="leave blank to keep current">
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check"></i> Save Changes</button>
                    <a href="{{ route('admin.cc-partners.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
