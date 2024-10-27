{{-- @dd($errors) --}}

<div class="text-end mb-4">
    <a href="manager_export?data={{ json_encode(request()->query()) }}"
        class="btn btn-primary">Export Attendance</a>
</div>
<div class="table-responsive">
    <table class="table table-striped custom-table mb-0">
        <thead>
            <tr>
                <th>#</th>
                <th>Date </th>
                <th>Employee</th>
                <th>Designation</th>
                <th>Attendence Visual</th>
                <th>Effective Hrs</th>
                <th><center>Gross Hrs</center></th>
                <th><center>Arrival</center></th>
                <th><center>Log</center></th>
{{--                <th>Action</th>--}}
            </tr>
        </thead>
        <tbody>
            {{-- @dd($newAttendence) --}}
            @foreach ($newAttendence as $key => $userAttendance)
                @foreach ($userAttendance as $attendence)
                    @php
                        $attendence = (object) $attendence;
                        // // dd(gettype($attendence));
                        // try {
                        //     $attendence->a_date;
                        // } catch (\Throwable $th) {
                        //     // Convert stdClass to associative array
                        //     $arrayData = json_decode(json_encode($attendence), true);

                        //     // Convert associative array to object
                        //     $attendence = (object) $arrayData;
                        // dd($attendence);
                        // }
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $attendence->a_date }}</td>
                        <td>{{ $attendence->user->first_name }} {{ $attendence->user->last_name }}</td>
                        <td>{{ $attendence->user->employee_details->designation->name }}</td>
                        <td>
                            <div class="progress progress-xs">
                                <div class="progress-bar progress-bar-striped bg-primary" role="progressbar"
                                    style="width: {{ $attendence->attendence_visual }}%"
                                    aria-valuenow="{{ $attendence->attendence_visual }}" aria-valuemin="0"
                                    aria-valuemax="100">
                                </div>
                            </div>
                        </td>
                        <td>
                            <h6>{{ $attendence->effective_hrs_in_hours }} Hrs
                                {{ $attendence->effective_hrs_in_minus }} mins
                            </h6>
                        </td>
                        <td>
                            <center>
                                <h6>{{ $attendence->Gross_Hrs }}</h6>
                            </center>
                        </td>
                        <td>
                            <center>
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
                            </center>
                        </td>
                        <td>
                            <center>
                                @if (
                                    $attendence->status == \App\Enums\AttendenceEnum::OnTime->value ||
                                        $attendence->status == \App\Enums\AttendenceEnum::Late->value)
                                    <button class="btn btn-primary"
                                            onclick="openLogModal('{{ json_encode($attendence) }}')">View</button>
                                @endif
                            </center>

                        </td>
                        {{-- <td>
                            @if ($attendence->status == \App\Enums\AttendenceEnum::OnTime->value)
                                <span class="bg-success text-white" style="padding: 8px 8px; border-radius: 100%;"><i
                                        class="fa fa-check"></i></span>
                            @elseif($attendence->status == \App\Enums\AttendenceEnum::Late->value)
                                <span class="bg-primary text-white" style="padding: 8px 13px; border-radius: 100%;"><i
                                        class="fa fa-exclamation"></i></span>
                            @elseif($attendence->status == \App\Enums\AttendenceEnum::Leave->value)
                                <span class="bg-info text-white" style="padding: 8px 13px; border-radius: 100%;"><i
                                        class="fa fa-exclamation"></i></span>
                            @elseif($attendence->status == \App\Enums\AttendenceEnum::Holiday->value)
                                <span class="bg-warning text-white" style="padding: 8px 13px; border-radius: 100%;"><i
                                        class="fa fa-exclamation"></i></span>
                            @elseif($attendence->status == \App\Enums\AttendenceEnum::Absent->value)
                                <span class="bg-danger text-white" style="padding: 8px 13px; border-radius: 100%;"><i
                                        class="fa fa-exclamation"></i></span>
                            @endif
                        </td> --}}
                        {{--<td>
                            --}}{{-- @dd($attendence) --}}{{--
                            --}}{{-- <button class="btn btn-primary btn-sm"
                                onclick="editModal('{{ json_encode($attendence) }}');">Edit</button> --}}{{--
                            <button class="btn btn-primary btn-sm"
                                onclick="editModal('{{ json_encode($attendence) }}');">Edit</button>
                        </td>--}}
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>

@include('employee.manager.attendence.regular.components.modals.editmodal2')
@include('employee.manager.attendence.regular.components.modals.logmodal')
@push('modal-script')
    <script src="{{ asset('assets/js/attendence/main.js') }}"></script>
    <script>
        function editModal(data) {
            let mainData = JSON.parse(data);

            let modalView = modalViewfunc(mainData);

            $('#editmodaldata').html(modalView);
            $("#edit_attendence").modal('show');
            $('.timepicker').datetimepicker({
                format: 'hh:mm A', // Display only hours and minutes
                // stepping: 1
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
                    </tr>
                </thead>
                <tbody>
            `;

            let subString = ``;
            attendence = JSON.parse(attendence);
            console.log(attendence);
            for (let index = 0; index < attendence['logs'].length; index++) {
                const element = attendence['logs'][index];
                console.log(element);

                var utcDateCheckIn = new Date(element['arrival_time']);
                let newyorkCheckIn = new Intl.DateTimeFormat('en-US', {
                    timeZone: 'America/New_York',
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                }).format(utcDateCheckIn);

                var utcDateCheckOut = new Date(element['leave_time']);
                let newyorkCheckOut = new Intl.DateTimeFormat('en-US', {
                    timeZone: 'America/New_York',
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                }).format(utcDateCheckOut);

                let checkin = formatLogDate(newyorkCheckIn);
                let checkout = formatLogDate(newyorkCheckOut);
                subString += `
                <tr>
                    <td>${checkin}</td>
                    <td>${checkout}</td>
                </tr>
                `;
            }
            mainString += `${subString}</tbody></table>`;

            $('#view_modal_data').html(mainString);
        }


        function formatLogDate(datetime) {

            // Parse the timestamp into a Date object
            let date = new Date(datetime);

            // Format the date
            let formattedDate = date.toLocaleString('en-US', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: 'numeric',
                minute: '2-digit',
                hour12: true // Use 12-hour clock format
            });
            return formattedDate;
        }
    </script>
@endpush
