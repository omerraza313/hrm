@extends('admin.layout.main')
@section('main-container')
    @push('title')
        Employee Payroll - Vibeh
    @endpush
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Employee Salary</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Salary</li>
                </ul>
            </div>
            {{-- <div class="col-auto float-end ms-auto">
                <a href="#" class="btn add-btn" data-bs-toggle="modal" data-bs-target="#add_salary"><i
                        class="fa fa-plus"></i> Add Salary</a>
            </div> --}}
        </div>
    </div>
    <!-- /Page Header -->

    @php
        $year = request()->year ?? now()->format('Y');
        $month = request()->month ?? now()->format('m');
        $salary_date = $year . '-' . $month . '-01' ?? now()->format('Y-m-d');
    @endphp

    @include('admin.payroll.components.view.filter')

    @include('admin.payroll.components.view.table')
@endsection
