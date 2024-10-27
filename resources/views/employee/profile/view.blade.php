@extends('employee.layout.main')
@section('main-container')
    @push('title')
        Profile - Vibeh
    @endpush
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Profile</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('employee.dashboard') }}">Dashboard</a></li>
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
                    @include('employee.profile.components.profileinfo')
                </div>
            </div>
        </div>
    </div>

    <div class="tab-content">

        <!-- Profile Info Tab -->
        <div id="emp_profile" class="pro-overview tab-pane fade show active">
            <div class="row">
                <div class="col-md-6 d-flex">
                    @include('employee.profile.components.personalinfo')
                </div>
                <div class="col-md-6 d-flex">
                    @include('employee.profile.components.emergenceycontect')
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 d-flex">
                    @include('employee.profile.components.bankinfo')
                </div>
                <div class="col-md-6 d-flex">
                    <div class="card profile-box flex-fill">
                        @include('employee.profile.components.familyinfo')
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 d-flex">
                    <div class="card profile-box flex-fill">
                        @include('employee.profile.components.educationinformation')
                    </div>
                </div>
                <div class="col-md-6 d-flex">
                    <div class="card profile-box flex-fill">
                        @include('employee.profile.components.experienceinfo')
                    </div>
                </div>
            </div>
        </div>
        <!-- /Profile Info Tab -->

    </div>
@endsection
