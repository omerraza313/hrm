@extends('admin.layout.main')
@section('main-container')
    @push('title')
        Profile - Vibeh
    @endpush
    <!-- Page Header -->
    @php
        $role_manager_status = false;
        $roles_string = '';
        foreach ($employee_roles as $index => $role) {
            if (\App\Enums\RolesEnum::Manager->value == $role) {
                $role_manager_status = true;
            }

            if ($index == 0) {
                $roles_string .= ucfirst($role);
            } else {
                $roles_string .= ', ' . ucfirst($role);
            }
        }
    @endphp
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Profile</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.employee.all') }}">Employees</a></li>
                    <li class="breadcrumb-item active">Profile</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="card mb-0">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    @include('admin.profile.components.profileinfo')
                </div>
            </div>
        </div>
    </div>

    <div class="tab-content">

        <!-- Profile Info Tab -->
        <div id="emp_profile" class="pro-overview tab-pane fade show active">
            <div class="row">
                <div class="col-md-6 d-flex">
                    @include('admin.profile.components.personalinfo')
                </div>
                <div class="col-md-6 d-flex">
                    @include('admin.profile.components.emergenceycontect')
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 d-flex">
                    @include('admin.profile.components.bankinfo')
                </div>
                <div class="col-md-6 d-flex">
                    <div class="card profile-box flex-fill">
                        @include('admin.profile.components.familyinfo')
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 d-flex">
                    <div class="card profile-box flex-fill">
                        @include('admin.profile.components.educationinformation')
                    </div>
                </div>
                <div class="col-md-6 d-flex">
                    <div class="card profile-box flex-fill">
                        @include('admin.profile.components.experienceinfo')
                    </div>
                </div>
            </div>
            <div class="row">
                @if ($employee->deactive_user)
                    <div class="col-md-6 d-flex">
                        <div class="card profile-box flex-fill">
                            @include('admin.profile.components.deactive')
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <!-- /Profile Info Tab -->

    </div>
@endsection
