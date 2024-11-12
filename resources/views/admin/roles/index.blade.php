@extends('admin.layout.main')
@section('main-container')
    @push('title')
        Leave Setting - Vibeh
    @endpush

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Roles</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Roles</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
            <div class="card leave-box" id="leave_annual">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12" style="text-align: right;">
                            @can('role.create')
                            <a class="btn btn-primary" href="{{route('roles.create')}}">
                                Create Role </a>
                            @endcan
                        </div>
                    </div>
                    @include('admin.roles.components.views.roles-table')
                </div>
            </div>
        </div>
    </div>
@endsection

