@extends('employee.layout.main')
@section('main-container')
    @push('title')
        Vibeh
    @endpush
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Welcome {{ $employee->first_name }}!</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('employee.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Employee Dashboard</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card mb-0">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    @include('employee.dashboard.components.views.profile')
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-5">
        <div class="card-body">
            @include('employee.dashboard.components.views.stats')
        </div>
    </div>


    <div class="card mt-5">
        <div class="card-body">
            @include('employee.dashboard.components.views.attendence')
        </div>
    </div>
@endsection
