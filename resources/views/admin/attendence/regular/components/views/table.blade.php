@php
use App\Helpers\DateHelper;
@endphp
<div class="row align-items-center">

    <div class="col-lg-10">
        <h4>Attendances history</h4>
    </div>
    <div class="col-lg-2 text-end">
     
    </div>
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User </th>
                        <th>Date </th>
                        <th>Attendence visual</th>
                        <th>Shift Start</th>
                        <th>Leniency</th>
                        <th>Late Hours</th>
                        <th>Arrival</th>
                        <th>Earned Hrs</th>
                        <th>Effective Hrs</th>
                        <th>Gross Hrs</th>
                        <th>Status</th>
                        <th>Log</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($flattenedAttendanceData as $key=>$data)
                    @php
                    $data = (object) $data;
                    //dd($attendence);
                    //dd($attendence->user);
                    $user = App\Models\User::find($data->user_id);
                    @endphp
                    <tr>
                        <td>{{++$key}}</td>
                        <td>{{$user->full_name ?? 'NA'}}</td>
                        <td>{{Carbon\Carbon::parse($data->date)->format('m/d/y')}}</td>
                        <td>
                            <div class="progress progress-xs">
                                <div class="progress-bar progress-bar-striped bg-primary" role="progressbar"
                                     style="width: {{ $data->attendence_visual }}%"
                                     aria-valuenow="{{ $data->attendence_visual }}" aria-valuemin="0"
                                     aria-valuemax="100">
                                </div>
                            </div>
                        </td>
                        <td>{{$data->shift_start}}</td>
                        <td>{{$data->leniency}}</td>
                        <td>{{$data->lateFormattedTime}}</td>
                        <td>
                            @if($data->checkin_time)
                            {{ \Carbon\Carbon::parse($data->checkin_time)->format('h:i:s A') }}
                            @else
                            --:--:--
                            @endif
                        </td>
                        <td>{{$data->earned_time}}</td>
                        <td>{{$data->effective_time}}</td>
                        <td>{{$data->gross_time}}</td>
                        <td>
                            @if ($data->status == \App\Enums\AttendenceEnum::OnTime->value)
                                <span class="bg-success text-white" style="padding: 8px 8px; border-radius: 100%;"><i
                                        class="fa fa-check"></i></span>
                                {{ \App\Helpers\AttendenceHelper::getattendenceStatusName($data->status) }}
                            @elseif ($data->status == \App\Enums\AttendenceEnum::Late->value)
                                <span class="bg-primary text-white" style="padding: 8px 13px; border-radius: 100%;"><i
                                        class="fa fa-exclamation"></i></span>
                                {{ \App\Helpers\AttendenceHelper::getattendenceStatusName($data->status) }}
                            @elseif($data->status == \App\Enums\AttendenceEnum::Leave->value)
                                <span class="bg-info text-white" style="padding: 8px 13px; border-radius: 100%;"><i
                                        class="fa fa-exclamation"></i></span>
                                {{ \App\Helpers\AttendenceHelper::getattendenceStatusName($data->status) }}
                            @elseif($data->status == \App\Enums\AttendenceEnum::Holiday->value)
                                <span class="bg-warning text-white" style="padding: 8px 13px; border-radius: 100%;"><i
                                        class="fa fa-exclamation"></i></span>
                                {{ \App\Helpers\AttendenceHelper::getattendenceStatusName($data->status) }}
                            @elseif($data->status == \App\Enums\AttendenceEnum::Absent->value)
                                <span class="bg-danger text-white" style="padding: 8px 13px; border-radius: 100%;"><i
                                        class="fa fa-exclamation"></i></span>
                                {{ \App\Helpers\AttendenceHelper::getattendenceStatusName($data->status) }}
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-primary" onclick="fetchDeviceLogs('{{ $data->date }}', {{ $data->user_id }})">View</button>
                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>

@include('employee.attendence.components.modals.logmodal')
@push('modal-script')
<script>
    function getAttendenceLog() {
        let value = document.getElementById('attendence_log_month').value;
        window.location.href = "{{ route('employee.attendence.view') }}" + "?filterMonth=" + value;
    }

    function fetchDeviceLogs(arrivalDate, userId) {
    $.ajax({
        url: '{{ route('fetch.device_log') }}', // Correctly namespaced route
        method: 'GET',
        data: {
            arrival_date: arrivalDate,
            user_id: userId
        },
        success: function(response) {
            $('#view_log_modal').modal('show'); // Show the modal

            let totalFormattedTime = calculateEarnedTime(response);
            let effective_time = calculateEffectiveTime(response, arrivalDate);
            let formattedDate = formatDate(arrivalDate);

            let mainString = `
                <table class="table">
                    <thead>
                        <tr>
                        
                            <th>Date</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Earned Time</th>
                            <th>Floor</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            response.forEach(log => {
                let checkin = log.checkin ? convertTo12HourFormat(log.checkin) : '';
                let checkout = log.checkout ? convertTo12HourFormat(log.checkout) : '';
                mainString += `
                    <tr>
                        <td>${formattedDate}</td>
                        <td>${checkin}</td>
                        <td>${checkout}</td>
                        <td>${log.time_spent}</td>
                        <td>${log.device_id}</td>
                        <td>${log.remarks}</td>
                    </tr>
                `;
            });

            mainString += `
                <tr>
                    <td colspan="3"><strong>Earned Time</strong></td>
                    <td colspan="3">${totalFormattedTime}</td>
                </tr>
                <tr>
                    <td colspan="3"><strong>Effective Time</strong></td>
                    <td colspan="3">${effective_time}</td>
                </tr>
                <tr>
                     <td colspan="3"><strong>Gross Hours</strong></td>
                    <td colspan="3">08:00:00</td>
                </tr>

            `;

            mainString += `
                    </tbody>
                </table>
            `;

            $('#view_modal_data').html(mainString);
        },
        error: function(xhr, status, error) {
            console.error(error);
        }
    });
}

function formatDate(dateString) {
    let date = new Date(dateString); 
    let month = String(date.getMonth() + 1).padStart(2, '0'); 
    let day = String(date.getDate()).padStart(2, '0'); 
    let year = date.getFullYear(); 

    return `${month}/${day}/${year}`; 
}

            function convertTo12HourFormat(timeStr) {
                let [hours, minutes, seconds] = timeStr.split(':');

                hours = parseInt(hours);
                if (hours > 12) {
                    hours = hours - 12;
                } else if (hours === 0) {
                    hours = 12; // Midnight case
                } else if (hours === 12) {
                    hours = 12; // Noon case
                }
                hours = hours < 10 ? '0' + hours : hours;
                return `${hours}:${minutes}:${seconds}`;
            }

        function calculateEffectiveTime(logs, arrivalDate) {
            if (!logs.length) return "00:00:00"; // If no logs, return zero time.
            let checkInTimes = logs.map(log => convertToSeconds(log.checkin)).filter(time => time !== null);
            let checkOutTimes = logs.map(log => convertToSeconds(log.checkout)).filter(time => time !== null);
            if (!checkInTimes.length || !checkOutTimes.length) return "00:00:00"; // If missing times, return zero time.

            let firstCheckIn = Math.min(...checkInTimes);
            let lastCheckOut = Math.max(...checkOutTimes);

            let differenceInSeconds = lastCheckOut - firstCheckIn;

            let hours = Math.floor(differenceInSeconds / 3600);
            let minutes = Math.floor((differenceInSeconds % 3600) / 60);
            let seconds = differenceInSeconds % 60; // Ensure correct seconds value

            return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }


        function convertToSeconds(timeStr) {
            if (!timeStr) return null;

            let parts = timeStr.split(':'); 

            let hours = parseInt(parts[0], 10); // Explicit base-10 conversion
            let minutes = parseInt(parts[1], 10); 
            let seconds = parts.length === 3 ? parseInt(parts[2], 10) : 0; // Ensure seconds are accounted for

            return hours * 3600 + minutes * 60 + seconds;
        }


        function calculateEarnedTime(logs) {
    let totalEarnedTimeInSeconds = 0;

    logs.forEach(log => {
        if (log.time_spent) {
            totalEarnedTimeInSeconds += convertToSeconds(log.time_spent);
        }
    });

    return convertSecondsToTime(totalEarnedTimeInSeconds);
}

        function convertSecondsToTime(totalSeconds) {
            let hours = Math.floor(totalSeconds / 3600);
            let minutes = Math.floor((totalSeconds % 3600) / 60);
            let seconds = totalSeconds % 60;

            return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }
</script>
@endpush