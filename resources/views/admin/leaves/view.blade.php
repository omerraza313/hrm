@extends('admin.layout.main')
@section('main-container')
    @push('title')
        Leave Setting - Vibeh
    @endpush

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Leave Category</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <!-- <li class="breadcrumb-item"><a href="#">HR</a></li>
                    <li class="breadcrumb-item"><a href="#">Leaves</a></li> -->
                    <li class="breadcrumb-item active">Leave Categories</li>
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
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#define_leave_type">
                                Define Leave Type
                            </button>
                        </div>
                    </div>
                    @include('admin.leaves.components.views.leaves-table')
                </div>
            </div>
        </div>
    </div>
    @include('admin.leaves.components.modals.define-leave-modal')
    @include('admin.leaves.components.modals.define-leave-edit-modal')
    @include('admin.leaves.components.modals.define-leave-delete-modal')
@endsection

