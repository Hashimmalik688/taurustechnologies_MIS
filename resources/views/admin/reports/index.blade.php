@extends('layouts.master')

@section('title')
    Reports
@endsection

@section('css')
    {{-- <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" /> --}}
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Reports
        @endslot
        @slot('title')
            Reports
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

                <div class="card-body">
                    <form method="GET" action="{{ route('reports.index') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="employee" class="form-label required">Employee</label>
                                    <select name="employee" id="employee"
                                        class="form-select @error('employee') is-invalid @enderror">
                                        <option value="">Select Employee</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}"
                                                {{ old('employee') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('employee')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="fromDate" class="form-label required">From Date</label>
                                    <input type="date" class="form-control @error('from_date') is-invalid @enderror"
                                        id="fromDate" name="from_date" placeholder="Enter From Date"
                                        value="{{ old('from_date') }}">

                                    @error('from_date')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="toDate" class="form-label required">To Date</label>
                                    <input type="date" class="form-control @error('to_date') is-invalid @enderror"
                                        id="toDate" name="to_date" placeholder="Enter To Date"
                                        value="{{ old('to_date') }}">
                                    @error('to_date')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div>
                            <button type="submit" class="btn btn-primary w-md">Submit</button>
                        </div>
                    </form>

                    @if (isset($leads) && $leads->count() > 0)
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered table-nowrap" id="datatable">
                                <thead>
                                    <tr>
                                        <th>Lead ID</th>
                                        <th>Lead Name</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($leads as $lead)
                                        <tr>
                                            <td>{{ $lead->id }}</td>
                                            <td>{{ $lead->cn_name }}</td>
                                            <td>{{ $lead->created_at }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div> <!-- end col -->
    </div>
@endsection

@section('script')
    {{-- <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script> --}}
@endsection
