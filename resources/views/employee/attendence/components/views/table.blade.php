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
                        <th>Arrival</th>
                        <th>Earned Hrs</th>
                        <th>Effective Hrs</th>
                        <th>Gross Hrs</th>
                        <th>Log</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($newAttendanceData as $key=>$data)
                        <tr>
                            <td>{{++$key}}</td>
                            <td>{{$data['date']}}</td>
                            <td>
                                <div class="progress progress-xs">
                                    <div class="progress-bar progress-bar-striped bg-primary" role="progressbar"
                                        style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </td>
                            <td>
                                @if($data['checkin_time'])
                                    {{ \Carbon\Carbon::parse($data['checkin_time'])->format('h:i A') }}
                                @else
                                    --:--:--
                                @endif
                            </td>
                            <td>{{$data['earned_time']}}</td>
                            <td>{{$data['effective_time']}}</td>
                            <td>08:00:00</td>
                            <td>
                            <button class="btn btn-primary" onclick="fetchDeviceLogs('{{ $data['date'] }}', {{ $data['user_id'] }})">View</button>
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

                    // Loop through the response data to create table rows
                    response.forEach(log => {
                        mainString += `
                            <tr>
                                <td>${log.checkin}</td>
                                <td>${log.checkout}</td>
                                <td>${log.time_spent}</td>
                                <td>${log.device_id}</td>
                                <td><!-- Optional: Add remarks here --></td>
                            </tr>
                        `;
                    });

                    mainString += `
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
