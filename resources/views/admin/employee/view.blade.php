@extends('admin.layout.main')
@section('main-container')
    @push('title')
        Employee - Vibeh
    @endpush
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Employees</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.employee.all') }}">Employees</a></li>
                    <li class="breadcrumb-item active">Employees List</li>
                </ul>
            </div>

            <div class="col-auto float-end ms-auto">
                <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_employee"><i
                        class="fa fa-plus"></i> Add Employee</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <!-- Search Filter -->
    @include('admin.employee.components.search')
    <!-- Search Filter -->

    <div class="row staff-grid-row mt-4">
        @if (isset($employees) && $employees)
            @foreach ($employees as $employee)
                <div class="col-md-4 col-sm-6 col-12 col-lg-4 col-xl-3">
                    <div class="profile-widget">
                        <div class="profile-img">
                            <a href="{{ route('profile.view', $employee->id) }}" class="avatar"><img
                                    src="{{ $employee->image ? asset('images/employee/') . '/' . $employee->image : asset('assets/img/profiles/avatar-02.jpg') }}"
                                    alt=""></a>
                        </div>
                        <div class="dropdown profile-action">
                            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false"><i class="material-icons">more_vert</i></a>
                            <div class="dropdown-menu dropdown-menu-right">
                                @php
                                    $image = $employee->image
                                        ? asset('images/employee/') . '/' . $employee->image
                                        : asset('assets/img/profiles/avatar-02.jpg');
                                @endphp
                                @if (request()->status == 'deactive')
                                    <a class="dropdown-item" href="#"
                                        onclick="openDeactiveViewModal('{{ $employee }}');"><i
                                            class="fa fa-pencil-o m-r-5"></i> View</a>
                                    <a class="dropdown-item" href="#"
                                        onclick="openReactiveModal('{{ route('admin.employee.restore', $employee->id) }}');"><i
                                            class="fa fa-pencil-o m-r-5"></i> Reactive</a>
                                @else
                                    <a class="dropdown-item" href="#"
                                        onclick="openEditModal('{{ route('admin.employee.update', $employee->id) }}', '{{ json_encode($employee) }}', '{{ $image }}', '{{ json_encode($employee->roles->pluck('name')) }}');"><i
                                            class="fa fa-pencil m-r-5"></i> Edit</a>
                                    <a class="dropdown-item" href="#"
                                        onclick="openDeleteModal('{{ route('admin.employee.delete', $employee->id) }}');"><i
                                            class="fa fa-trash-o m-r-5"></i> Deactive</a>
{{--                                    <p>{{ json_encode($employee) }}</p>--}}
                                @endif

                            </div>
                        </div>
                        <h4 class="user-name m-t-10 mb-0 text-ellipsis">
                            <a href="{{ route('profile.view', $employee->id) }}">{{ $employee->first_name }}
                                {{ $employee->last_name }}</a>
                        </h4>
                        <div class="small text-muted">{{ $employee->employee_details->pseudo_name }}</div>
                        <div class="small text-muted">{{ $employee->employee_details->designation->name }}</div>
                    </div>
                </div>
            @endforeach
        @endif

    </div>
    @include('admin.employee.add')
    @include('admin.employee.edit')
    @include('admin.employee.delete')
    @include('admin.employee.reactive')
    @include('admin.employee.modals.deactiveview')
    @php
        $edit_modal_status = false;
        foreach ($errors->getMessages() as $field => $messages) {
            if (substr($field, 0, 5) == 'edit_') {
                $edit_modal_status = true;
            }
        }
        $delete_modal_status = false;
        foreach ($errors->getMessages() as $field => $messages) {
            if (substr($field, 0, 7) == 'delete_') {
                $delete_modal_status = true;
            }
        }
    @endphp
    @push('modal-script')
        <script src="{{ asset('assets/js/employee/crud.js') }}"></script>
    @endpush
    @if ($edit_modal_status)
        @push('modal-script')
            <script src="{{ asset('assets/js/employee/modal.js') }}"></script>
        @endpush
    @endif

    @if ($delete_modal_status)
        @push('modal-script')
            <script>
                openDeleteModal('{{ old('route_name') }}');
            </script>
        @endpush
    @endif
@endsection
