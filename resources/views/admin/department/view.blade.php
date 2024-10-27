@extends('admin.layout.main')
@section('main-container')
    @push('title')
        Department - Vibeh
    @endpush
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Departments</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.employee.all') }}">Employees</a></li>
                    <li class="breadcrumb-item active">Departments</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_department"><i
                        class="fa fa-plus"></i> Add Department</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
            <div>
                <table class="table table-striped custom-table mb-0 datatable">
                    <thead>
                        <tr>
                            <th style="width: 30px;">#</th>
                            <th>Department Name</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($departments)
                            @foreach ($departments as $department)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $department->name }}</td>
                                    <td class="text-end">
                                        <div class="dropdown dropdown-action">
                                            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                                                aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="#"
                                                    onclick="openEditModal('{{ route('admin.department.update', $department->id) }}', '{{ $department->name }}', '{{ $department->id }}')"><i
                                                        class="fa fa-pencil m-r-5"></i>
                                                    Edit</a>
                                                <a class="dropdown-item" href="#"
                                                    onclick="openDeleteModal('{{ route('admin.department.delete', $department->id) }}')"><i
                                                        class="fa fa-trash-o m-r-5"></i>
                                                    Delete</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                @include('admin.department.delete')
                            @endforeach
                        @endif

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('admin.department.add')

    @include('admin.department.edit')

    @push('modal-script')
        <script src="{{ asset('assets/js/department/crud.js') }}"></script>
    @endpush

    @if ($errors->has('edit_name'))
        @push('modal-script')
            <script src="{{ asset('assets/js/department/modal.js') }}"></script>
        @endpush
    @endif
@endsection
