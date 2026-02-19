@use('App\Support\Roles')
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
                                    <label for="plain_password" class="form-label">Password (Plaintext Reference)</label>
                                    <input type="text" class="form-control" id="plain_password" name="plain_password"
                                        value="{{ old('plain_password', $user->userDetail->plain_password ?? '') }}"
                                        placeholder="Enter password for reference">
                                    <small class="text-muted">This is for reference only, not for login</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
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
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Roles</label>
                                    @php
                                        $currentRoles = $user->roles->pluck('name')->toArray();
                                    @endphp
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="{{ Roles::SUPER_ADMIN }}" id="role-super-admin" 
                                                    {{ in_array(Roles::SUPER_ADMIN, old('roles', $currentRoles)) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role-super-admin">{{ Roles::SUPER_ADMIN }}</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="{{ Roles::MANAGER }}" id="role-manager"
                                                    {{ in_array(Roles::MANAGER, old('roles', $currentRoles)) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role-manager">{{ Roles::MANAGER }}</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="{{ Roles::HR }}" id="role-hr"
                                                    {{ in_array(Roles::HR, old('roles', $currentRoles)) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role-hr">{{ Roles::HR }}</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="{{ Roles::EMPLOYEE }}" id="role-employee"
                                                    {{ in_array(Roles::EMPLOYEE, old('roles', $currentRoles)) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role-employee">{{ Roles::EMPLOYEE }}</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="{{ Roles::COORDINATOR }}" id="role-co-ordinator"
                                                    {{ in_array(Roles::COORDINATOR, old('roles', $currentRoles)) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role-co-ordinator">{{ Roles::COORDINATOR }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="{{ Roles::QA }}" id="role-qa"
                                                    {{ in_array(Roles::QA, old('roles', $currentRoles)) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role-qa">{{ Roles::QA }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label text-primary">Peregrine Team</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="{{ Roles::PEREGRINE_CLOSER }}" id="role-peregrine-closer"
                                                    {{ in_array(Roles::PEREGRINE_CLOSER, old('roles', $currentRoles)) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role-peregrine-closer">{{ Roles::PEREGRINE_CLOSER }}</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="{{ Roles::PEREGRINE_VALIDATOR }}" id="role-peregrine-validator"
                                                    {{ in_array(Roles::PEREGRINE_VALIDATOR, old('roles', $currentRoles)) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role-peregrine-validator">{{ Roles::PEREGRINE_VALIDATOR }}</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="{{ Roles::VERIFIER }}" id="role-verifier"
                                                    {{ in_array(Roles::VERIFIER, old('roles', $currentRoles)) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role-verifier">{{ Roles::VERIFIER }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="{{ Roles::RAVENS_CLOSER }}" id="role-ravens-closer"
                                                    {{ in_array(Roles::RAVENS_CLOSER, old('roles', $currentRoles)) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role-ravens-closer">{{ Roles::RAVENS_CLOSER }}</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="{{ Roles::RETENTION_OFFICER }}" id="role-retention-officer"
                                                    {{ in_array(Roles::RETENTION_OFFICER, old('roles', $currentRoles)) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role-retention-officer">{{ Roles::RETENTION_OFFICER }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    @error('roles')
                                        <div class="text-danger mt-2">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
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
    .form-check {
        margin-bottom: 0.5rem;
    }
    .form-check-label {
        font-weight: 500;
    }
    .text-primary {
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: block;
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 0.25rem;
    }
</style>
@endsection

@section('script')
@endsection
