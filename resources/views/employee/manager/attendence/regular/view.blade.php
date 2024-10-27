@extends('employee.layout.main')

@section('main-container')
    @push('title')
        Attendence - Vibeh
    @endpush
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Attendance</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Manager</a></li>
                    <li class="breadcrumb-item"><a href="#">Attendance</a></li>
                    <li class="breadcrumb-item active">View Attendance</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    @include('employee.manager.attendence.regular.components.views.stats')

    <div class="row">
        <div class="col-lg-12">
            @include('employee.manager.attendence.regular.components.views.table')
        </div>
    </div>
@endsection
