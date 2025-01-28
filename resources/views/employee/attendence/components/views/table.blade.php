@php
use App\Helpers\DateHelper;
@endphp
<div class="row align-items-center">

    <div class="col-lg-10">
        <h4>Attendance Log - 30 days</h4>
    </div>
    <div class="col-lg-2 text-end">
        <div class="form-group">
            <select class="form-control" onchange="getAttendenceLog();" id="attendence_log_month">
                @foreach (\App\Helpers\AttendenceHelper::get_capital_months() as $key => $month)
                <option value="{{ $key }}" @if (isset($_GET['filterMonth']) && $key==$_GET['filterMonth']) selected @endif>
                    {{ $month }}
                </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date </th>
                        <th>Attendence visual</th>
                        <th>Shift Start</th>
                        <th>Leniency</th>
                        <!-- <th>Late Hours</th> -->
                        <th>Arrival</th>
                        <th>Earned Hrs</th>
                        <th>Effective Hrs</th>
                        <th>Gross Hrs</th>
                        <th>Status</th>
                        <th>Log</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($newAttendanceData as $key=>$data)
                    @php
                    $data = (object) $data;
                    //dd($data);
                    //dd($attendence->user);

                    @endphp
                    <tr>
                        <td>{{++$key}}</td>
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
                        <!-- <td>{{""}}</td> -->
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
                            <button class="btn btn-primary" onclick="fetchDeviceLogs('{{ $data->date }}', '{{ $data->user_id }}')">View</button>
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
            url: '{{ route('employee.fetch.device_log') }}', // Correctly namespaced route
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

                // Loop through the response data to create table rows
                response.forEach(log => {
                    mainString += `
                            <tr>
                                <td>${formattedDate}</td>
                                <td>${log.checkin}</td>
                                <td>${log.checkout}</td>
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
                            </tbody>
                        </table>
                    `;

                // Set the generated HTML into the modal content
                $('#view_modal_data').html(mainString);
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }
    function formatDate(dateString) {
    let date = new Date(dateString); // Convert to Date object
    let month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based, so add 1 and pad with zero
    let day = String(date.getDate()).padStart(2, '0'); // Pad the day with leading zero if necessary
    let year = date.getFullYear(); // Get the full year

    return `${month}/${day}/${year}`; // Return formatted date as mm/dd/yyyy
}
 
function calculateEffectiveTime(logs, arrivalDate) {
    if (!logs.length) return "00:00:00"; // No logs, return zero time.
    // Extract check-in and check-out times without sorting
    console.log(logs);
    let checkInTimes = logs.map(log => convertToSeconds(log.checkin)).filter(time => time !== null);
    let checkOutTimes = logs.map(log => convertToSeconds(log.checkout)).filter(time => time !== null);

    if (!checkInTimes.length || !checkOutTimes.length) return "00:00:00"; // If missing times, return zero time.

    let firstCheckIn = checkInTimes[0];  // First check-in from logs order
    let lastCheckOut = checkOutTimes[checkOutTimes.length - 1]; // Last check-out from logs order

    if (lastCheckOut < firstCheckIn) return "00:00:00"; 

    let differenceInSeconds = lastCheckOut - firstCheckIn;
    
    return convertSecondsToTime(differenceInSeconds);
}


function calculateEarnedTime(logs) {
    let totalEarnedTimeInSeconds = 0;

    logs.forEach(log => {
        if (log.checkin && log.checkout) {
            let checkInTimeInSeconds = convertToSeconds(log.checkin);
            let checkOutTimeInSeconds = convertToSeconds(log.checkout);

            // Ensure checkout time is after checkin time
            if (checkOutTimeInSeconds > checkInTimeInSeconds) {
                totalEarnedTimeInSeconds += (checkOutTimeInSeconds - checkInTimeInSeconds);
            }
        }
    });

    return convertSecondsToTime(totalEarnedTimeInSeconds);
}
function convertToSeconds(timeStr) {
    if (!timeStr) return null;

    let parts = timeStr.split(':'); 

    let hours = parseInt(parts[0], 10); // Explicit base-10 conversion
    let minutes = parseInt(parts[1], 10); 
    let seconds = parts.length === 3 ? parseInt(parts[2], 10) : 0; // Ensure seconds are accounted for

    return hours * 3600 + minutes * 60 + seconds;
}
function convertSecondsToTime(totalSeconds) {
    let hours = Math.floor(totalSeconds / 3600);
    let minutes = Math.floor((totalSeconds % 3600) / 60);
    let seconds = totalSeconds % 60;

    return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
}


    function formatMilliseconds(ms) {
        let totalSeconds = Math.floor(ms / 1000);
        let totalMinutes = Math.floor(totalSeconds / 60);
        let totalHours = Math.floor(totalMinutes / 60);

        let seconds = totalSeconds % 60;
        let minutes = totalMinutes % 60;
        let hours = totalHours;

        // Ensure two digits for hours, minutes, and seconds
        let formattedHours = String(hours).padStart(2, '0');
        let formattedMinutes = String(minutes).padStart(2, '0');
        let formattedSeconds = String(seconds).padStart(2, '0');

        // Construct the formatted time string
        let formattedTime = `${formattedHours}:${formattedMinutes}:${formattedSeconds}`;

        return formattedTime;
    }

    function calculateTotalTimeDifference(logs) {
        let totalTimeDifference = 0;

        for (let index = 0; index < logs.length; index++) {
            const element = logs[index];

            let checkin = new Date(element['arrival_time']);
            let checkout = new Date(element['leave_time']);

            // Calculate the difference in milliseconds and add it to the total
            totalTimeDifference += Math.abs(checkout - checkin);
        }
        return formatMilliseconds(totalTimeDifference);
    }

    function calculateTimeDifference(datetime1, datetime2) {
        // Parse the timestamps into Date objects
        let date1 = new Date(datetime1);
        let date2 = new Date(datetime2);

        // Calculate the difference in milliseconds
        let timeDifference = Math.abs(date2 - date1);

        return formatMilliseconds(timeDifference)

        // return formattedDifference || '00:00:00';
    }

    function formatLogDate(datetime) {
        // Parse the timestamp into a Date object
        let date = new Date(datetime);

        // Add 4 hours (4 hours * 60 minutes * 60 seconds * 1000 milliseconds)
        date.setTime(date.getTime() + 4 * 60 * 60 * 1000);

        // Format the date with seconds, 24-hour format, and timezone
        let formattedDate = date.toLocaleString('en-US', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit', // Include seconds
            hour12: false, // Use 24-hour clock format
            //timeZoneName: 'short' // Include timezone abbreviation
        });

        return formattedDate;
    }
</script>
@endpush