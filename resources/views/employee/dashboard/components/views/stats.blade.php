<div class="row">
    <div class="col-lg-3 my-2">
        <div class="row">
            <div class="col-lg-3 col-3">
                <span class="bg-info text-white p-2 rounded" style="font-size: 30px">
                    <i class="la la-user-check"></i>
                </span>
            </div>
            <div class="col-lg-9 col-9">
                <h4 style="margin-bottom: 0px;">Current Status</h4>
                <span style="font-size: 13px; font-weight: 300">On</span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 my-2">
        <div class="row">
            <div class="col-lg-3 col-3">
                <span class="bg-success text-white p-2 rounded" style="font-size: 30px">
                    <i class="la la-sign-out-alt"></i>
                </span>
            </div>
            <div class="col-lg-9 col-9">
                <h4 style="margin-bottom: 0px;">Checked In</h4>
                <span
                    style="font-size: 13px; font-weight: 300">{{ $lastAttendence?->arrival_time ? \Carbon\Carbon::parse($lastAttendence->arrival_time)->setTimezone('America/New_York')->format('h:i A') : '' }}</span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 my-2">
        <div class="row">
            <div class="col-lg-3 col-3">
                <span class="bg-danger text-white p-2 rounded" style="font-size: 30px">
                    <i class="la la-briefcase"></i>
                </span>
            </div>
            <div class="col-lg-9 col-9">
                <h4 style="margin-bottom: 0px;">Working Policy</h4>
                <span style="font-size: 13px; font-weight: 300">
                    @if (!empty($employee->policy->toArray()))
                        {{ $employee->policy[0]->working_settings->shift_start }}
                        -
                        {{ $employee->policy[0]->working_settings->shift_close }}
                    @endif
                </span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 my-2">
        <div class="row">
            <div class="col-lg-3 col-3">
                <span class="bg-warning text-white p-2 rounded" style="font-size: 30px">
                    <i class="la la-clock"></i>
                </span>
            </div>
            <div class="col-lg-9 col-9">
                <h4 style="margin-bottom: 0px;">Expected / Earned Hours</h4>
                <span style="font-size: 13px; font-weight: 300">{{ $totalWorkingHours }} Hrs / {{ $workingHours }}
                    Hrs</span>
            </div>
        </div>
    </div>
</div>
