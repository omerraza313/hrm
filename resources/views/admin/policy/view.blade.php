@extends('admin.layout.main')
@section('main-container')
    @push('title')
        Policy - Vibeh
    @endpush

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Policies</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <!-- <li class="breadcrumb-item"><a href="#">HR</a></li> -->
                    <li class="breadcrumb-item active">Policies</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_policy"><i
                        class="fa fa-plus"></i> Add Policy</a>
            </div>
            <div class="col-auto float-end ms-auto">
                <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#swap_policy"><i
                        class="fa fa-exchange"></i> Swap Policy</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    <div class="row">
        <div class="col-md-12">
            @include('admin.policy.components.views.table')
        </div>
    </div>

    @include('admin.policy.components.modals.addmodal')
    @include('admin.policy.components.modals.swapmodal')

    @php
        $add_modal_status = false;
        foreach ($errors->getMessages() as $field => $messages) {
            if (substr($field, 0, 11) == 'add_policy_') {
                $add_modal_status = true;
            }
        }

        $swap_modal_status = false;
        foreach ($errors->getMessages() as $field => $messages) {
            if (substr($field, 0, 5) == 'swap_') {
                $swap_modal_status = true;
            }
        }
    @endphp
    @if ($add_modal_status)
        @push('modal-script')
            {{-- <script src="{{ asset('assets/js/employee/crud.js') }}"></script> --}}
            <script>
                $(document).ready(function() {
                    // Show the modal on page load
                    $("#add_policy").modal('show');
                });
            </script>
        @endpush
    @endif
    @if ($swap_modal_status)
        @push('modal-script')
            {{-- <script src="{{ asset('assets/js/employee/crud.js') }}"></script> --}}
            <script>
                $(document).ready(function() {
                    // Show the modal on page load
                    $("#swap_policy").modal('show');
                });
            </script>
        @endpush
    @endif
@endsection
