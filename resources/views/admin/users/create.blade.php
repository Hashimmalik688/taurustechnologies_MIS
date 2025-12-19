@extends('layouts.master')

@section('title')
    Create User
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Users
        @endslot
        @slot('title')
            Create User
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
                    {{-- <h4 class="card-title mb-4">Form Grid Layout</h4> --}}

                    <form method="POST" action="{{ route('users.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label required">Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" placeholder="Enter Name">

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
                                        id="email" name="email" placeholder="Enter Email ID">

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
                                        placeholder="Enter Phone">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="zoomNumber" class="form-label">Zoom Number</label>
                                    <input type="text" class="form-control" id="zoomNumber" name="zoom_number"
                                        placeholder="Enter Zoom number">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="password" class="form-label required">Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" placeholder="Enter Password">

                                    @error('password')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label required">Confirm Password</label>
                                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                        id="password_confirmation" name="password_confirmation" placeholder="Confirm Password">

                                    @error('password_confirmation')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Roles</label>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="Super Admin" id="role-super-admin">
                                                <label class="form-check-label" for="role-super-admin">Super Admin</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="Manager" id="role-manager">
                                                <label class="form-check-label" for="role-manager">Manager</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="HR" id="role-hr">
                                                <label class="form-check-label" for="role-hr">HR</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="Employee" id="role-employee">
                                                <label class="form-check-label" for="role-employee">Employee</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="Agent" id="role-agent">
                                                <label class="form-check-label" for="role-agent">Agent</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="Vendor" id="role-vendor">
                                                <label class="form-check-label" for="role-vendor">Vendor</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="QA" id="role-qa">
                                                <label class="form-check-label" for="role-qa">QA</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="Trainer" id="role-trainer">
                                                <label class="form-check-label" for="role-trainer">Trainer</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label text-primary">Paraguins Team</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="Paraguins Closer" id="role-paraguins-closer">
                                                <label class="form-check-label" for="role-paraguins-closer">Paraguins Closer</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="Paraguins Validator" id="role-paraguins-validator">
                                                <label class="form-check-label" for="role-paraguins-validator">Paraguins Validator</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="Verifier" id="role-verifier">
                                                <label class="form-check-label" for="role-verifier">Verifier</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="Ravens Closer" id="role-ravens-closer">
                                                <label class="form-check-label" for="role-ravens-closer">Ravens Closer</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="Retention Officer" id="role-retention-officer">
                                                <label class="form-check-label" for="role-retention-officer">Retention Officer</label>
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
                                        placeholder="Enter DOB">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select id="gender" name="gender" class="form-select">
                                        <option value="">Select gender...</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="join-date" class="form-label">Join Date</label>
                                    <input type="date" class="form-control" id="join-date" name="join_date">
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="city" name="city"
                                        placeholder="Enter City">
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="address" name="address"
                                        placeholder="Enter Address">
                                </div>
                            </div>
                        </div>

                        <div>
                            <button type="submit" class="btn btn-primary w-md">Submit</button>
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
