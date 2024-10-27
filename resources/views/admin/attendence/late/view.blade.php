@extends('admin.layout.main')

@section('main-container')
    @push('title')
        Late Comers - Vibeh
    @endpush
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Late Comers</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <!-- <li class="breadcrumb-item"><a href="#">HR</a></li>
                    <li class="breadcrumb-item"><a href="#">Attendance</a></li> -->
                    <li class="breadcrumb-item active">Late Comers</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    @include('admin.attendence.late.components.views.stats')

    <div class="row">
        <div class="col-lg-12">
            @include('admin.attendence.late.components.views.table')
        </div>
    </div>
@endsection
