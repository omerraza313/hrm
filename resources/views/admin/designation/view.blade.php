@extends('admin.layout.main')
@section('main-container')
    @push('title')
        Designation - Vibeh
    @endpush
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Designations</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.employee.all') }}">Employees</a></li>
                    <li class="breadcrumb-item active">Designations</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_designation"><i
                        class="fa fa-plus"></i> Add Designation</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped custom-table mb-0 datatable">
                    <thead>
                        <tr>
                            <th style="width: 30px;">#</th>
                            <th>Designation </th>
                            <th>Department </th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($designations) && !empty($designations))
                            @foreach ($designations as $designation)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $designation->name }}</td>
                                    <td>{{ $designation->department->name ?? '' }}</td>
                                    <td class="text-end">
                                        <div class="dropdown dropdown-action">
                                            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                                                aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="#"
                                                    onclick="openEditModal('{{ route('admin.designation.update', $designation->id) }}', '{{ $designation->name ?? '' }}', '{{ $designation->department->id ?? '' }}');"><i
                                                        class="fa fa-pencil m-r-5"></i>
                                                    Edit</a>
                                                <a class="dropdown-item" href="#"
                                                    onclick="openDeleteModal('{{ route('admin.designation.delete', $designation->id) }}')"><i
                                                        class="fa fa-trash-o m-r-5"></i>
                                                    Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('admin.designation.add')
    @include('admin.designation.edit')
    @include('admin.designation.delete')

    @push('modal-script')
        <script src="{{ asset('assets/js/designation/crud.js') }}"></script>
    @endpush

    @if ($errors->has('edit_name') || $errors->has('edit_department'))
        @push('modal-script')
            <script src="{{ asset('assets/js/designation/modal.js') }}"></script>
        @endpush
    @endif
@endsection
