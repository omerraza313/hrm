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
                    <li class="breadcrumb-item"><a href="{{ route('employee.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Attendance</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->
     {{--
    <div class="row">
        @include('employee.attendence.components.views.stats')

        @include('employee.attendence.components.views.timing')

        @include('employee.attendence.components.views.action')
    </div>
    --}}
    @include('employee.attendence.components.views.table')
    
@endsection
