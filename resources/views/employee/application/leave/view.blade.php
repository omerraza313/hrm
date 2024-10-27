@extends('employee.layout.main')
@section('main-container')
    @push('title')
        Leave Application - Vibeh
    @endpush
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Leave Applications</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('employee.dashboard') }}">Dashboard</a></li>
                    <!-- <li class="breadcrumb-item"><a href="#">Leave</a></li> -->
                    <li class="breadcrumb-item">Leave Applications</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_leave"><i
                        class="fa fa-plus"></i> Add Leave</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    @include('employee.application.leave.components.views.stats')
    @include('employee.application.leave.components.views.table')
    @include('employee.application.leave.components.modals.addleave')
@endsection
