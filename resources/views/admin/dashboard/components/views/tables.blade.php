<div class="row">
    <div class="col-md-6 d-flex">
        <div class="card card-table flex-fill">
            <div class="card-header">
                <h3 class="card-title mb-0">Recent Leaves</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-nowrap custom-table mb-0">
                        <thead>
                            <tr>
                                <th>Id#</th>
                                <th>Leave Type</th>
                                <th>Employee</th>
                                <th>No of Days</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @dd($leaves) --}}
                            @if (isset($leaves))
                                @foreach ($leaves as $leave)
                                    <tr>
                                        <td>{{ $leave->id }}</td>
                                        <td>{{ $leave->subject }}</td>
                                        <td>{{ $leave->employee?->first_name }} {{ $leave->employee?->last_name }}</td>
                                        <td>{{ \App\Helpers\DateHelper::calculateNumberOfDays($leave->leave_from, $leave->leave_upto) }}
                                            days</td>
                                        <td>
                                            @if ($leave->status == \App\Enums\ApprovedStatusEnum::Pending->value)
                                                <span class="badge bg-inverse-warning">Pending</span>
                                            @elseif($leave->status == \App\Enums\ApprovedStatusEnum::Approved->value)
                                                <span class="badge bg-inverse-success">Approved</span>
                                            @elseif($leave->status == \App\Enums\ApprovedStatusEnum::Declined->value)
                                                <span class="badge bg-inverse-danger">Declined</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.leave.application.view') }}">View all Leaves</a>
            </div>
        </div>
    </div>
    <div class="col-md-6 d-flex">
        <div class="card card-table flex-fill">
            <div class="card-header">
                <h3 class="card-title mb-0">Upcomming Birthdays</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table custom-table table-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>Emp Id</th>
                                <th>Name</th>
                                <th>Date of Birth</th>
                                <th>Department</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($birthdays))
                                @foreach ($birthdays as $birthday)
                                    <tr>
                                        <td>{{ $birthday->id }}</td>
                                        <td>{{ $birthday->first_name }} {{ $birthday->last_name }}</td>
                                        <td>{{ \App\Helpers\DateHelper::dateFormat('m/d/Y',$birthday?->employee_details?->dob) }}</td>
                                        <td>{{ $birthday->employee_details?->department?->name }}</td>
                                        {{-- <td>{{ count($designation->users) }}</td> --}}
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.employee.all') }}">View all Employees</a>
            </div>
        </div>
    </div>
    {{-- <div class="col-md-6 d-flex">
        <div class="card card-table flex-fill">
            <div class="card-header">
                <h3 class="card-title mb-0">Designations</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table custom-table table-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Total Employees</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($designations))
                                @foreach ($designations as $designation)
                                    <tr>
                                        <td>{{ $designation->id }}</td>
                                        <td>{{ $designation->name }}</td>
                                        <td>{{ $designation->department->name }}</td>
                                        <td>{{ count($designation->users) }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.designation.all') }}">View all Designations</a>
            </div>
        </div>
    </div> --}}
</div>
