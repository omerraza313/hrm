@extends('admin.layout.main')
@section('main-container')
    @push('title')
        Employee Payroll - Vibeh
    @endpush

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Payslip</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="admin-dashboard.html">Dashboard</a></li>
                    <li class="breadcrumb-item active">Payslip</li>
                </ul>
            </div>
            <div class="col-auto float-end ms-auto">
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-white"><i class="fa fa-print fa-lg"></i> Print</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    @php
        $originalDate = request()->date;
        // Convert to Carbon instance
        $carbonDate = \Carbon\Carbon::createFromFormat('Y-m-d', $originalDate);

        // Format the date as "M Y" (e.g., "Feb 2019")
        $salary_date = $carbonDate->format('M Y');
    @endphp
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="payslip-title">Payslip for the month of {{ $salary_date }}</h4>
                    <div class="row">
                        <div class="col-sm-6 m-b-20">
                            <img src="{{ asset('images/auth/newlogo.png') }}" class="inv-logo" alt=""
                                style="width: 250px;">
                            <ul class="list-unstyled mb-0">
                                <li>Dreamguy's Technologies</li>
                                <li>3864 Quiet Valley Lane,</li>
                                <li>Sherman Oaks, CA, 91403</li>
                            </ul>
                        </div>
                        <div class="col-sm-6 m-b-20">
                            <div class="invoice-details">
                                <h3 class="text-uppercase">Payslip #49029</h3>
                                <ul class="list-unstyled">
                                    <li>Salary Month: <span>{{ $salary_date }}</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 m-b-20">
                            <ul class="list-unstyled">
                                <li>
                                    <h5 class="mb-0"><strong>{{ $employee->first_name }}
                                            {{ $employee->last_name }}</strong></h5>
                                </li>
                                <li><span>{{ $employee->employee_details->designation->name }}</span></li>
                                <li>Employee ID: {{ $employee->id }}</li>
                                <li>Joining Date: {{ $employee->employee_details->join_date }}</li>
                            </ul>
                        </div>
                        <div class="col-sm-6 m-b-20">

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div>
                                <h4 class="m-b-10"><strong>Earnings</strong></h4>
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td><strong>Basic Salary</strong> <span class="float-end">Rs
                                                    {{ number_format($employee->salary->max('salary')) }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>House Rent Allowance (H.R.A.)</strong> <span class="float-end">Rs
                                                    0</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Conveyance</strong> <span class="float-end">Rs 0</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Other Allowance</strong> <span class="float-end">Rs 0</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Earnings</strong> <span class="float-end"><strong>Rs
                                                        {{ number_format(request()->earnsalary ?? 0) }}</strong></span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div>
                                <h4 class="m-b-10"><strong>Deductions</strong></h4>
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td><strong>Tax Deducted at Source (T.D.S.)</strong> <span class="float-end">Rs
                                                    0</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Provident Fund</strong> <span class="float-end">Rs 0</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>ESI</strong> <span class="float-end">Rs 0</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Loan</strong> <span class="float-end">Rs 0</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Deductions</strong> <span class="float-end"><strong>Rs
                                                        0</strong></span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
