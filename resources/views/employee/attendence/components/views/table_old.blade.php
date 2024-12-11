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
                    <option value="{{ $key }}" @if (isset($_GET['filterMonth']) && $key == $_GET['filterMonth']) selected @endif>
                        {{ $month }}</option>
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
                        <th>Earned Hrs</th>
                        <th>Effective Hrs</th>
                        <th>Gross Hrs</th>
                        <th>Arrival</th>
                        <th>Log</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($newAttendence as $key => $attendence)
                        @php
                            $attendence = (object) $attendence;
                            $effectiveTime = DateHelper::calculateEffectiveTime($attendence->logs);
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ Date('m/d/Y', strtotime($attendence->a_date)) }}</td>
                            <td>
                                <div class="progress progress-xs">
                                    <div class="progress-bar progress-bar-striped bg-primary" role="progressbar"
                                        style="width: {{ $attendence->attendence_visual }}%"
                                        aria-valuenow="{{ $attendence->attendence_visual }}" aria-valuemin="0"
                                        aria-valuemax="100"></div>
                                </div>
                            </td>
                            <td>
                                <h6>
                                    {{ $attendence->effective_hrs_in_hours }} Hrs
                                    {{ $attendence->effective_hrs_in_minus }} mins
                                </h6>
                            </td>
                            <td>
                                <h6>
                                    {{ $effectiveTime }}
                                </h6>
                            </td>
                            <td>
                                <h6>{{ $attendence->Gross_Hrs }}
                                </h6>
                            </td>
                            <td>
                                @if ($attendence->status == \App\Enums\AttendenceEnum::OnTime->value)
                                    <span class="bg-success text-white"
                                        style="padding: 8px 8px; border-radius: 100%;"><i
                                            class="fa fa-check"></i></span>
                                    {{ \App\Helpers\AttendenceHelper::getattendenceStatusName($attendence->status) }}
                                @elseif ($attendence->status == \App\Enums\AttendenceEnum::Late->value)
                                    <span class="bg-primary text-white"
                                        style="padding: 8px 13px; border-radius: 100%;"><i
                                            class="fa fa-exclamation"></i></span>
                                    {{ \App\Helpers\AttendenceHelper::getattendenceStatusName($attendence->status) }}
                                @elseif($attendence->status == \App\Enums\AttendenceEnum::Leave->value)
                                    <span class="bg-info text-white" style="padding: 8px 13px; border-radius: 100%;"><i
                                            class="fa fa-exclamation"></i></span>
                                    {{ \App\Helpers\AttendenceHelper::getattendenceStatusName($attendence->status) }}
                                @elseif($attendence->status == \App\Enums\AttendenceEnum::Holiday->value)
                                    <span class="bg-warning text-white"
                                        style="padding: 8px 13px; border-radius: 100%;"><i
                                            class="fa fa-exclamation"></i></span>
                                    {{ \App\Helpers\AttendenceHelper::getattendenceStatusName($attendence->status) }}
                                @elseif($attendence->status == \App\Enums\AttendenceEnum::Absent->value)
                                    <span class="bg-danger text-white"
                                        style="padding: 8px 13px; border-radius: 100%;"><i
                                            class="fa fa-exclamation"></i></span>
                                    {{ \App\Helpers\AttendenceHelper::getattendenceStatusName($attendence->status) }}
                                @endif
                            </td>
                            <td><button class="btn btn-primary"
                                    onclick="openLogModal('{{ json_encode($attendence) }}')">View</button>
                            </td>
                            {{-- <td>
                                @if ($attendence->status == \App\Enums\AttendenceEnum::OnTime->value)
                                    <span class="bg-success text-white"
                                        style="padding: 8px 8px; border-radius: 100%;"><i
                                            class="fa fa-check"></i></span>
                                @elseif($attendence->status == \App\Enums\AttendenceEnum::Late->value)
                                    <span class="bg-primary text-white"
                                        style="padding: 8px 13px; border-radius: 100%;"><i
                                            class="fa fa-exclamation"></i></span>
                                @elseif($attendence->status == \App\Enums\AttendenceEnum::Leave->value)
                                    <span class="bg-info text-white" style="padding: 8px 13px; border-radius: 100%;"><i
                                            class="fa fa-exclamation"></i></span>
                                @elseif($attendence->status == \App\Enums\AttendenceEnum::Holiday->value)
                                    <span class="bg-warning text-white"
                                        style="padding: 8px 13px; border-radius: 100%;"><i
                                            class="fa fa-exclamation"></i></span>
                                @elseif($attendence->status == \App\Enums\AttendenceEnum::Absent->value)
                                    <span class="bg-danger text-white"
                                        style="padding: 8px 13px; border-radius: 100%;"><i
                                            class="fa fa-exclamation"></i></span>
                                @endif
                            </td> --}}
                        </tr>
                    @endforeach
                    {{-- <tr>
                        <td>1</td>
                        <td>19 Feb 2019</td>
                        <td>
                            <div class="progress progress-xs">
                                <div class="progress-bar progress-bar-striped bg-primary" role="progressbar"
                                    style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </td>
                        <td>
                            <h6>5 Hrs, 31 min</h6>
                        </td>
                        <td>
                            <h6>9 Hrs</h6>
                        </td>
                        <td>
                            <span class="bg-warning text-white" style="padding: 8px 13px; border-radius: 100%;"><i
                                    class="fa fa-exclamation"></i></span> 3 hrs late
                        </td>
                        <td>
                            <span class="bg-warning text-white" style="padding: 8px 13px; border-radius: 100%;"><i
                                    class="fa fa-exclamation"></i></span>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>19 Feb 2019</td>
                        <td>
                            <div class="progress progress-xs">
                                <div class="progress-bar progress-bar-striped bg-primary" role="progressbar"
                                    style="width: 70%" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </td>
                        <td>
                            <h6>5 Hrs, 31 min</h6>
                        </td>
                        <td>
                            <h6>9 Hrs</h6>
                        </td>
                        <td>
                            <span class="bg-success text-white" style="padding: 8px 8px; border-radius: 100%;"><i
                                    class="fa fa-check"></i></span> On Time
                        </td>
                        <td>
                            <span class="bg-success text-white" style="padding: 8px 8px; border-radius: 100%;"><i
                                    class="fa fa-check"></i></span>
                        </td>
                    </tr> --}}
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

        function openLogModal(attendence) {
            $('#view_log_modal').modal('show');

            let mainString = `
            <table class="table">
                <thead>
                    <tr>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Earned Time</th>
                        <th>Floor</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
            `;

            let subString = ``;
            attendence = JSON.parse(attendence);
            console.log(attendence);
            let totalFormattedTime = calculateTotalTimeDifference(attendence['logs']);
            let effective_time = calculateEffectiveTime(attendence['logs']);

            for (let index = 0; index < attendence['logs'].length; index++) {
                const element = attendence['logs'][index];
                let checkin = formatLogDate(element['arrival_time']);
                let checkout = formatLogDate(element['leave_time']);

                let remarks = element['remarks'] ?? '';
                let device_id = element['device_id'] ?? '';
                let time_diff = calculateTimeDifference(checkin, checkout);

                subString += `
                <tr>
                    <td>${checkin}</td>
                    <td>${checkout}</td>
                    <td>${time_diff}</td>
                    <td>${device_id}</td>
                    <td>${remarks}</td>
                </tr>
                `;
            }
            mainString += `${subString}
            <tr>
                <td colspan="2"><strong>Earned Time</strong></td>

                <td colspan="3">${totalFormattedTime}</td>
            </tr>
            <tr>
                <td colspan="2"><strong>Effective Time</strong></td>
                <td colspan="3">${effective_time}</td>
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
    </script>
@endpush
