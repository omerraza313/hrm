<div class="table-responsive">
    <table class="table table-striped custom-table mb-0">
        <thead>
            <tr>
                <th>#</th>
                <th>Date </th>
                <th>Employee Details</th>
                <th>Attendence Visual</th>
                <th>Effective Hrs</th>
                <th>Gross Hrs</th>
                <th>Arrival</th>
                <th>Log</th>
            </tr>
        </thead>
        <tbody>
            {{-- @dd($newAttendence) --}}
            @foreach ($newAttendence as $key => $userAttendance)
                @foreach ($userAttendance as $attendence)
                    @php
                        $attendence = (object) $attendence;
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ Date('m/d/Y', strtotime($attendence->a_date)) }}</td>
                        <td>{{ $attendence->user->first_name }} {{ $attendence->user->last_name }}
                            (<small
                                style="color: #4e4e4e">{{ $attendence->user->employee_details->designation->name }}</small>)
                        </td>
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
                            <h6>{{ $attendence->Gross_Hrs }}
                            </h6>
                        </td>
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
                        {{-- <td><button class="btn btn-primary"
                                onclick="openLogModal('{{ json_encode($attendence) }}')">View</button>
                        </td> --}}
                        <td>
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
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>

@include('admin.attendence.late.components.modals.logmodal')
@push('modal-script')
    <script>
        function openLogModal(attendence) {
            $('#view_log_modal').modal('show');

            let mainString = `
            <table class="table">
                <thead>
                    <tr>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Time</th>
                        <th>Floor</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
            `;

            let subString = ``;
            attendence = JSON.parse(attendence);
            console.log(attendence);
            for (let index = 0; index < attendence['logs'].length; index++) {
                const element = attendence['logs'][index];
                // console.log(element);
                console.log(element['arrival_time']);

                let checkin = formatLogDate(newyorkCheckIn);
                let checkout = formatLogDate(newyorkCheckOut);
                let times = '-';
                let device_id = element('device_id') ?? '-';
                let remarks = element('remarks') ?? '-';
                subString += `
                <tr>
                    <td>${checkin}</td>
                    <td>${checkout}</td>
                    <td>${times}</td>
                    <td>${device_id}</td>
                    <td>${remarks}</td>
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
