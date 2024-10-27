@extends('admin.layout.main')
@section('main-container')
    @push('title')
        Vibeh
    @endpush
    {{-- <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Welcome {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}!</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active">Dashboard</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header --> --}}

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Welcome {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}!</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Admin Dashboard</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">

            </div>
        </div>
    </div>
    <!-- /Page Header -->


    @include('admin.dashboard.components.views.totals')

    @include('admin.dashboard.components.views.graphs')

    @include('admin.dashboard.components.views.tables')
@endsection
