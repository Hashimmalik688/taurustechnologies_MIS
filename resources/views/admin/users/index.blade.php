@extends('layouts.master')

@section('title')
    Users List
@endsection

@section('css')
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Users
        @endslot
        @slot('title')
            List
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
        <div class="col-12">
            <div class="card">
                <div class="d-flex justify-content-end align-items-center p-2">
                    <div class="text-end mx-3">
                        <a class="btn btn-success btn-sm waves-effect waves-light" href="{{ route('users.create') }}">Add
                            User</a>
                    </div>
                </div>

                <div class="card-body">
                    <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Phone Number</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->userDetail->phone ?? 'N/A' }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->status === 'active')
                                            <span class="badge bg-success">Active</span>
                                        @elseif($user->status === 'inactive')
                                            <span class="badge bg-warning">Inactive</span>
                                        @elseif($user->status === 'suspended')
                                            <span class="badge bg-danger">Suspended</span>
                                        @else
                                            <span class="badge bg-secondary">Unknown</span>
                                        @endif
                                    </td>
                                    <td>
                                        @foreach ($user->roles as $role)
                                            <span class="badge bg-primary">{{ $role->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @hasrole('Super Admin|Manager')
                                            @if (!$user->roles->contains('name', 'Super Admin'))
                                                <a href="#" data-bs-toggle="modal"
                                                    data-bs-target="#edit-user-{{ $user->id }}"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="confirmDelete({{ $user->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        @endhasrole
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modals -->
    @foreach ($users as $user)
        <div class="modal fade" id="edit-user-{{ $user->id }}" tabindex="-1" aria-labelledby="editUserLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserLabel">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ $user->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ $user->email }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="suspended" {{ $user->status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone"
                                    value="{{ $user->userDetail->phone ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Roles</label>
                                @php
                                    $currentRoles = $user->roles->pluck('name')->toArray();
                                @endphp
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="Super Admin" id="modal-role-super-admin-{{ $user->id }}" 
                                                {{ in_array('Super Admin', $currentRoles) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="modal-role-super-admin-{{ $user->id }}">Super Admin</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="Manager" id="modal-role-manager-{{ $user->id }}"
                                                {{ in_array('Manager', $currentRoles) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="modal-role-manager-{{ $user->id }}">Manager</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="HR" id="modal-role-hr-{{ $user->id }}"
                                                {{ in_array('HR', $currentRoles) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="modal-role-hr-{{ $user->id }}">HR</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="Employee" id="modal-role-employee-{{ $user->id }}"
                                                {{ in_array('Employee', $currentRoles) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="modal-role-employee-{{ $user->id }}">Employee</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="Agent" id="modal-role-agent-{{ $user->id }}"
                                                {{ in_array('Agent', $currentRoles) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="modal-role-agent-{{ $user->id }}">Agent</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="Vendor" id="modal-role-vendor-{{ $user->id }}"
                                                {{ in_array('Vendor', $currentRoles) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="modal-role-vendor-{{ $user->id }}">Vendor</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-primary fw-bold">Paraguins Team</small>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="Paraguins Closer" id="modal-role-paraguins-closer-{{ $user->id }}"
                                                {{ in_array('Paraguins Closer', $currentRoles) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="modal-role-paraguins-closer-{{ $user->id }}">Paraguins Closer</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="Paraguins Validator" id="modal-role-paraguins-validator-{{ $user->id }}"
                                                {{ in_array('Paraguins Validator', $currentRoles) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="modal-role-paraguins-validator-{{ $user->id }}">Paraguins Validator</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="Verifier" id="modal-role-verifier-{{ $user->id }}"
                                                {{ in_array('Verifier', $currentRoles) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="modal-role-verifier-{{ $user->id }}">Verifier</label>
                                        </div>
                                        <hr class="my-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="Ravens Closer" id="modal-role-ravens-closer-{{ $user->id }}"
                                                {{ in_array('Ravens Closer', $currentRoles) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="modal-role-ravens-closer-{{ $user->id }}">Ravens Closer</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="Retention Officer" id="modal-role-retention-officer-{{ $user->id }}"
                                                {{ in_array('Retention Officer', $currentRoles) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="modal-role-retention-officer-{{ $user->id }}">Retention Officer</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="Trainer" id="modal-role-trainer-{{ $user->id }}"
                                                {{ in_array('Trainer', $currentRoles) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="modal-role-trainer-{{ $user->id }}">Trainer</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="QA" id="modal-role-qa-{{ $user->id }}"
                                                {{ in_array('QA', $currentRoles) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="modal-role-qa-{{ $user->id }}">QA</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password (Leave blank to keep current)</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Leave blank to keep current password">
                            </div>
                            <div class="mb-3">
                                <label for="dob" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="dob" name="dob"
                                    value="{{ $user->userDetail->dob ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-control" id="gender" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="Male"
                                        {{ ($user->userDetail->gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female"
                                        {{ ($user->userDetail->gender ?? '') == 'Female' ? 'selected' : '' }}>Female
                                    </option>
                                    <option value="Other"
                                        {{ ($user->userDetail->gender ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="join_date" class="form-label">Join Date</label>
                                <input type="date" class="form-control" id="join_date" name="join_date"
                                    value="{{ $user->userDetail->join_date ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3">{{ $user->userDetail->address ?? '' }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city"
                                    value="{{ $user->userDetail->city ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label for="zoom_number" class="form-label">Zoom Number</label>
                                <input type="text" class="form-control" id="zoom_number" name="zoom_number"
                                    value="{{ $user->zoom_number ?? '' }}">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
    <script>
        function confirmDelete(userId) {
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = `/users/delete/${userId}`;

            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }
    </script>
@endsection

@section('css')
<style>
    .modal .form-check {
        margin-bottom: 0.25rem;
    }
    .modal .form-check-label {
        font-size: 0.875rem;
        font-weight: 500;
    }
    .modal .text-primary {
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: block;
    }
    .modal-dialog {
        max-width: 600px;
    }
    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
</style>
