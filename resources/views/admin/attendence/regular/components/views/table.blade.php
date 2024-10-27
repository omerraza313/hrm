@php
    use App\Helpers\DateHelper;
    use Carbon\Carbon;
@endphp{{-- @dd($errors) --}}

<div class="text-end mb-4">
    <a href="{{ route('admin.attendence.export') }}?data={{ json_encode(request()->query()) }}"
        class="btn btn-primary">Export Attendance</a>
</div> &nbsp;

{{--<div class="text-end mb-4">--}}
{{--    <a href="{{ route('admin.attendence.adminexport') }}?data={{ json_encode(request()->query()) }}"--}}
{{--        class="btn btn-primary">Export Attendance (Admin)</a>--}}
{{--</div>--}}
<div class="table-responsive">
    <table class="table table-striped custom-table mb-0">
        <thead>
            <tr>
                <th>#</th>
                <th>Date </th>
                <th>Employee Details</th>
                <th>Shift Start</th>
                <th>Late Hours</th>
                <th>Attendence Visual</th>
                <th>Earned Hrs</th>
                <th>Effective Hrs</th>
                <th>Gross Hrs</th>
                <th>Arrival</th>
                <th>Log</th>
{{--                <th>Action</th>--}}
            </tr>
        </thead>
        <tbody>
            {{-- @dd($newAttendence) --}}
            @php
                $sn = 1;
                //dd($args);
                //dd(array_keys($newAttendence[12]));
            @endphp
            @foreach ($newAttendence as $this_user => $userAttendance)
                @php
                //dd($userAttendance);
                @endphp
                    @foreach ($userAttendance as $cur_date => $attendence)
                        @php
                            $attendence = (object) $attendence;
                           //dd($attendence);
                           //dd($attendence->user);

                        @endphp
                    <tr>
                        <td>{{ $sn++ }}</td>
                        <td>{{ Date('m/d/Y', strtotime($cur_date)) }}</td>
                        <td>{{ $attendence->user['data']['first_name'] }} {{ $attendence->user['data']['last_name'] }}
                            (<small
                                style="color: #4e4e4e">{{ $attendence->user['designation'] }}</small>)
                        </td>
                        <td> <h6>{{$attendence->shift_start}}</h6> </td>
                        <td> <h6>{{$attendence->late_time}}</h6> </td>
                        <td>
                            <div class="progress progress-xs">
                                <div class="progress-bar progress-bar-striped bg-primary" role="progressbar"
                                     style="width: {{ $attendence->attendence_visual }}%"
                                     aria-valuenow="{{ $attendence->attendence_visual }}" aria-valuemin="0"
                                     aria-valuemax="100">
                                </div>
                            </div>
                        </td>
                        <td> <h6>{{$attendence->earned_time}}</h6> </td>
                        <td> <h6>{{ $attendence->effective_time }}</h6> </td>
                        <td> <h6>{{$attendence->Gross_Hrs}} <input id="gross_time" value="{{$attendence->Gross_Hrs}}" type="hidden"></h6> </td>
                        <td>
                            @if ($attendence->status == \App\Enums\AttendenceEnum::OnTime->value)
                                <span class="bg-success text-white" style="padding: 8px 8px; border-radius: 100%;"><i
                                        class="fa fa-check"></i></span>
                                {{ \App\Helpers\AttendenceHelper::getattendenceStatusName($attendence->status) }}
                            @elseif ($attendence->status == \App\Enums\AttendenceEnum::Late->value)
                                <span class="bg-primary text-white" style="padding: 8px 13px; border-radius: 100%;"><i
                                        class="fa fa-exclamation"></i></span>
                                {{ \App\Helpers\AttendenceHelper::getattendenceStatusName($attendence->status) }}
                            @elseif($attendence->status == \App\Enums\AttendenceEnum::Leave->value)
                                <span class="bg-info text-white" style="padding: 8px 13px; border-radius: 100%;"><i
                                        class="fa fa-exclamation"></i></span>
                                {{ \App\Helpers\AttendenceHelper::getattendenceStatusName($attendence->status) }}
                            @elseif($attendence->status == \App\Enums\AttendenceEnum::Holiday->value)
                                <span class="bg-warning text-white" style="padding: 8px 13px; border-radius: 100%;"><i
                                        class="fa fa-exclamation"></i></span>
                                {{ \App\Helpers\AttendenceHelper::getattendenceStatusName($attendence->status) }}
                            @elseif($attendence->status == \App\Enums\AttendenceEnum::Absent->value)
                                <span class="bg-danger text-white" style="padding: 8px 13px; border-radius: 100%;"><i
                                        class="fa fa-exclamation"></i></span>
                                {{ \App\Helpers\AttendenceHelper::getattendenceStatusName($attendence->status) }}
                            @endif
                        </td>
                        <td>
                            @if (
                                $attendence->status == \App\Enums\AttendenceEnum::OnTime->value ||
                                $attendence->status == \App\Enums\AttendenceEnum::Late->value ||
                                $attendence->logs_count > 0
                                )
                                <button class="btn btn-primary"
                                    onclick="openLogModal('{{ json_encode($attendence, true) }}')">View</button>
                            @endif

                        </td>

{{--                        <td>--}}
{{--                            <button class="btn btn-primary btn-sm"--}}
{{--                                onclick="editModal('{{ json_encode($attendence) }}');">Edit</button>--}}
{{--                        </td>--}}
                    </tr>
                @endforeach
{{--                <tr>--}}
{{--                    <td colspan="7">--}}
{{--                        @php--}}
{{--                        echo "<pre>";--}}
{{--                        print_r($this_user);--}}
{{--                        @endphp--}}
{{--                    </td>--}}
{{--                </tr>--}}
            @endforeach
        </tbody>
    </table>
</div>

@include('admin.attendence.regular.components.modals.editmodal2')
@include('admin.attendence.regular.components.modals.logmodal')
@push('modal-script')
    <script src="{{ asset('assets/js/attendence/main.js') }}"></script>
    <script>
        function editModal(data) {
            let mainData = JSON.parse(data);
            let modalView = modalViewfunc(mainData);
            console.log('aatef');
            console.log(mainData);
            // alert('aatef');

            $('#editmodaldata').html(modalView);
            $("#edit_attendence").modal('show');
            $('.timepicker').datetimepicker({
                format: 'hh:mm A', // Display only hours and minutes
                // stepping: 1
            });
        }
        function formatTime24to12(time24) {
            const [hours, minutes] = time24.split(':');
            const ampm = hours >= 12 ? 'PM' : 'AM';
            const hours12 = hours % 12 || 12; // Convert 24-hour to 12-hour format, using 12 instead of 0
            return `${hours12}:${minutes} ${ampm}`;
        }

        function openLogModal(attendence) {
            $('#view_log_modal').modal('show');

            let subString = ``;
            attendence = JSON.parse(attendence);

            let mainString = `
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Check In (${attendence['shift_start']})</th>
                        <th>Check Out</th>
                        <th>Time</th>
                        <th>Floor</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
            `;


            // console.log('aatef is finally here');
            // console.log(attendence);
            let gross_time = document.getElementById('gross_time').value;
            //alert(gross_time);
            let totalFormattedTime = attendence['earned_time'];//calculateTotalTimeDifference(attendence['logs']);
            let effective_time = attendence['effective_time'];//calculateEffectiveTime(attendence['logs']);
            for (let index = 0; index < attendence['logs'].length; index++) {
                const element = attendence['logs'][index];
                console.log('what is in a element');
                console.log(element);

                let remarks = element['remarks'] ?? '';
                subString += `
                <tr>
                    <td>${element['date']}</td>
                    <td>${formatTime24to12(element['arrival_time'].split(' ')[1])}</td>
                    <td>${formatTime24to12(element['leave_time'].split(' ')[1])}</td>
                    <td>${element['earned_time']}</td>
                    <td>${element.device_id}</td>
                    <td>${remarks}</td>
                </tr>
                `;
            }
            mainString += `${subString}
                <tr>
                    <td colspan="3"><strong>Earned Time</strong></td>
                    <td colspan="3">${totalFormattedTime}</td>
                </tr>
                <tr>
                    <td colspan="3"><strong>Effective Time</strong></td>
                    <td colspan="3">${effective_time}</td>
                </tr>
                <tr>
                    <td colspan="3"><strong>Gross Time</strong></td>
                    <td colspan="3">${gross_time}</td>
                </tr>
            </tbody></table>`;

            $('#view_modal_data').html(mainString);
        }

        function calculateEffectiveTime(logs) {
            if (logs.length === 0) {
                return "00:00:00"; // Return if there are no logs
            }

            let earliestArrival = new Date(logs[0]['arrival_time']);
            let latestLeave = new Date(logs[0]['leave_time']);

            for (let index = 1; index < logs.length; index++) {
                const element = logs[index];

                let checkin = new Date(element['arrival_time']);
                let checkout = new Date(element['leave_time']);

                // Update the earliest arrival time
                if (checkin < earliestArrival) {
                    earliestArrival = checkin;
                }

                // Update the latest leave time
                if (checkout > latestLeave) {
                    latestLeave = checkout;
                }
            }

            // Calculate the total difference in milliseconds
            const totalTimeDifference = Math.abs(latestLeave - earliestArrival);

            return formatMilliseconds(totalTimeDifference);
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
                second: '2-digit',  // Include seconds
                hour12: false,       // Use 24-hour clock format
                //timeZoneName: 'short' // Include timezone abbreviation
            });

            return formattedDate;
        }
        function formatLogDateOnly(datetime) {
            // Parse the timestamp into a Date object
            let date = new Date(datetime);

            // Add 4 hours (4 hours * 60 minutes * 60 seconds * 1000 milliseconds)
            date.setTime(date.getTime() + 4 * 60 * 60 * 1000);

            // Format the date with year, month, day, and time (24-hour format)
            let formattedDate = date.toLocaleString('en-US', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                // hour: '2-digit',
                // minute: '2-digit',
                // second: '2-digit', // Include seconds
                hour12: false,     // Use 24-hour clock format
                timeZone: 'America/New_York' // Adjust timezone if needed
            });

            return formattedDate;
        }
        function formatLogTime(datetime) {
            // Parse the timestamp into a Date object
            let date = new Date(datetime);

            // Add 4 hours (4 hours * 60 minutes * 60 seconds * 1000 milliseconds)
            date.setTime(date.getTime() + 4 * 60 * 60 * 1000);

            // Format the time in 'hh:mm AM/PM' format
            let formattedTime = date.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true, // Use 12-hour clock format
                timeZone: 'America/New_York' // Adjust timezone if needed
            });

            return formattedTime;
        }
        function formatLogTime_his(datetime) {
            // Parse the timestamp into a Date object
            let date = new Date(datetime);

            // Add 4 hours (4 hours * 60 minutes * 60 seconds * 1000 milliseconds)
            date.setTime(date.getTime() + 4 * 60 * 60 * 1000);

            // Format the time with hours, minutes, and seconds (24-hour format)
            let formattedTime = date.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit', // Include seconds
                hour12: false,     // Use 24-hour clock format
                timeZone: 'America/New_York' // Adjust timezone if needed
            });

            return formattedTime;
        }
    </script>
@endpush
