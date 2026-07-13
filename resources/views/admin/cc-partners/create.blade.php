@extends('layouts.master')

@section('title') Add CC Partner @endsection

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h5 class="mb-1 fw-bold"><i class="bx bx-buildings me-1"></i> Add CC Partner</h5>
        <p class="text-muted mb-0" style="font-size:.82rem;">Creates an outsource sales company with its own portal login. It can then add its own closers.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.cc-partners.store') }}" method="POST" style="max-width:640px;">
                @csrf
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Company Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Code</label>
                        <input type="text" name="code" class="form-control" value="{{ old('code') }}" placeholder="auto if blank" maxlength="10">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Login Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="text" name="password" class="form-control" placeholder="min 8 characters" required>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check"></i> Create CC Partner</button>
                    <a href="{{ route('admin.cc-partners.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
