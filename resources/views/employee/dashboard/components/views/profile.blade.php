<div class="profile-view">
    <div class="profile-img-wrap">
        <div class="profile-img">
            <a href="#"><img alt=""
                    src="{{ $employee->image ? asset('images/employee/') . '/' . $employee->image : asset('assets/img/profiles/avatar-02.jpg') }}"></a>
        </div>
    </div>
    <div class="profile-basic">
        <div class="row">
            <div class="col-md-5 profile-info-left">

                <h3 class="user-name m-t-0 mb-0">
                    {{ $employee->first_name . ' ' . $employee->last_name }}</h3>
                <h6 class="text-muted" style="margin-bottom: 2px;">{{ $employee->employee_details->department->name }}
                </h6>
                <div class="small doj text-muted">Pseudo Name : {{ $employee->employee_details->pseudo_name }}</div>
                <small class="text-muted">{{ $employee->employee_details->designation->name }}</small>
                <div class="staff-id">Employee ID : {{ $employee->id }}</div>
                <div class="small doj text-muted">Date of Join :
                {{ \App\Helpers\DateHelper::dateFormat('m/d/Y',$employee->employee_details->join_date) }}</div>
                <div class="staff-msg"></div>

            </div>
            <div class="col-md-7 ps-2">
                <ul class="personal-info">
                    <li>
                        <div class="title">Phone :</div>
                        <div class="text"><a href="">{{ $employee->employee_details->phone }}</a></div>
                    </li>
                    <li>
                        <div class="title">Email :</div>
                        <div class="text"><a href="">{{ $employee->email }}</a></div>
                    </li>
                    <li>
                        <div class="title">Birthday :</div>
                        <div class="text">
                            {{ \App\Helpers\DateHelper::dateFormat('m/d/Y',$employee->employee_details->dob) }}
                        </div>
                        {{-- <div class="text">
                            {{ \App\Helpers\DateHelper::globaldateFormat('j M Y', $employee->employee_details->dob) }}
                        </div> --}}
                    </li>
                    <li>
                        <div class="title">Address :</div>
                        <div class="text">{{ $employee->address[0]->address }}</div>
                    </li>
                    <li>
                        <div class="title">Gender :</div>
                        <div class="text">{{ $employee->employee_details->gender }}</div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
