@extends('layouts.master')

@section('title')
    Edit User
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Users
        @endslot
        @slot('title')
            Edit User
        @endslot
    @endcomponent

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-all me-2"></i>
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-block-helper me-2"></i>
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('users.update', $user->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label required">Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', $user->name) }}"
                                        placeholder="Enter Name">

                                    @error('name')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label required">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email', $user->email) }}"
                                        placeholder="Enter Email ID">

                                    @error('email')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone"
                                        value="{{ old('phone', $user->userDetail->phone ?? '') }}"
                                        placeholder="Enter Phone">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="zoomNumber" class="form-label">Zoom Number</label>
                                    <input type="text" class="form-control" id="zoomNumber" name="zoom_number"
                                        value="{{ old('zoom_number', $user->userDetail->zoom_number ?? '') }}"
                                        placeholder="Enter Zoom number">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" placeholder="Leave blank to keep current password">

                                    @error('password')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <small class="text-muted">Leave blank if you don't want to change the password</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="role" class="form-label">Role</label>
                                    <select id="role" name="role" class="form-select">
                                        <option value="">Select role...</option>
                                        <option value="Super Admin" {{ old('role', $user->userDetail->role ?? '') == 'Super Admin' ? 'selected' : '' }}>Super Admin</option>
                                        <option value="Manager" {{ old('role', $user->userDetail->role ?? '') == 'Manager' ? 'selected' : '' }}>Manager</option>
                                        <option value="HR" {{ old('role', $user->userDetail->role ?? '') == 'HR' ? 'selected' : '' }}>HR</option>
                                        <option value="Employee" {{ old('role', $user->userDetail->role ?? '') == 'Employee' ? 'selected' : '' }}>Employee</option>
                                        <option value="Agent" {{ old('role', $user->userDetail->role ?? '') == 'Agent' ? 'selected' : '' }}>Agent</option>
                                        <option value="Vendor" {{ old('role', $user->userDetail->role ?? '') == 'Vendor' ? 'selected' : '' }}>Vendor</option>
                                        <optgroup label="Paraguins Team">
                                            <option value="Paraguins Closer" {{ old('role', $user->userDetail->role ?? '') == 'Paraguins Closer' ? 'selected' : '' }}>Paraguins Closer</option>
                                            <option value="Paraguins Validator" {{ old('role', $user->userDetail->role ?? '') == 'Paraguins Validator' ? 'selected' : '' }}>Paraguins Validator</option>
                                            <option value="Verifier" {{ old('role', $user->userDetail->role ?? '') == 'Verifier' ? 'selected' : '' }}>Verifier</option>
                                        </optgroup>
                                        <option value="Ravens Closer" {{ old('role', $user->userDetail->role ?? '') == 'Ravens Closer' ? 'selected' : '' }}>Ravens Closer</option>
                                        <option value="Retention Officer" {{ old('role', $user->userDetail->role ?? '') == 'Retention Officer' ? 'selected' : '' }}>Retention Officer</option>
                                        <option value="Trainer" {{ old('role', $user->userDetail->role ?? '') == 'Trainer' ? 'selected' : '' }}>Trainer</option>
                                        <option value="QA" {{ old('role', $user->userDetail->role ?? '') == 'QA' ? 'selected' : '' }}>QA</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="dob" class="form-label">DOB</label>
                                    <input type="date" class="form-control" id="dob" name="dob"
                                        value="{{ old('dob', $user->dob ? $user->dob->format('Y-m-d') : '') }}">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select id="gender" name="gender" class="form-select">
                                        <option value="">Select gender...</option>
                                        <option value="Male" {{ old('gender', $user->userDetail->gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ old('gender', $user->userDetail->gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                                        <option value="Other" {{ old('gender', $user->userDetail->gender ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="join-date" class="form-label">Join Date</label>
                                    <input type="date" class="form-control" id="join-date" name="join_date"
                                        value="{{ old('join_date', $user->userDetail->join_date ?? '') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="city" name="city"
                                        value="{{ old('city', $user->userDetail->city ?? '') }}"
                                        placeholder="Enter City">
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="address" name="address"
                                        value="{{ old('address', $user->userDetail->address ?? '') }}"
                                        placeholder="Enter Address">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-md">
                                <i class="mdi mdi-content-save me-1"></i>
                                Update User
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary w-md">
                                <i class="mdi mdi-arrow-left me-1"></i>
                                Back
                            </a>
                        </div>
                    </form>
                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
        </div>
    </div>
@endsection

@section('css')
<style>
    .required::after {
        content: " *";
        color: red;
    }
</style>
@endsection

@section('script')
@endsection
