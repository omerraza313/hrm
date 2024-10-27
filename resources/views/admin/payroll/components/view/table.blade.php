{{-- @dd($employees->toArray()) --}}
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table datatable">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Employee ID</th>
                        <th>Email</th>
                        <th>Join Date</th>
                        <th>Department</th>
                        <th>Salary</th>
                        <th>Payslip</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($employees))
                        @foreach ($employees as $employee)
                            @php
                                $total_earn_salary = \App\Helpers\SalaryHelper::calculate_salary($employee);
                            @endphp
                            <tr>
                                <td>
                                    <h2 class="table-avatar">
                                        <a href="{{ route('profile.view', $employee->id) }}" class="avatar"><img
                                                alt=""
                                                src="{{ $employee->image ? asset('images/employee/') . '/' . $employee->image : asset('assets/img/profiles/avatar-02.jpg') }}"></a>
                                        <a href="{{ route('profile.view', $employee->id) }}">{{ $employee->first_name . ' ' . $employee->last_name }}
                                            <span>{{ $employee->employee_details->designation->name }}</span></a>
                                    </h2>
                                </td>
                                <td>{{ $employee->id }}</td>
                                <td>{{ $employee->email }}</td>
                                <td>{{ \App\Helpers\DateHelper::dateFormat('j M Y', $employee->employee_details->join_date) }}
                                </td>
                                <td>
                                    {{ $employee->employee_details->department->name }}
                                </td>
                                <td>Rs
                                    {{ number_format($total_earn_salary) }}
                                </td>
                                {{-- <td>Rs {{ number_format($employee->employee_details->salary) }}</td> --}}
                                <td><a class="btn btn-sm btn-primary"
                                        href="{{ route('admin.payroll.employee.view') . '?id=' . $employee->id . '&date=' . $salary_date . '&earnsalary=' . $total_earn_salary }}">Generate
                                        Slip</a></td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
