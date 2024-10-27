<input type="hidden" id="calenderAttendence" value="{{ json_encode($newAttendences) }}">
<div class="row">
    <div class="col-lg-8">
        <div id='calendar'></div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h6>Sat Dec 16</h6>
                <span style="font-size: 12px; font-weight: 300;">Color Representation</span>
                <div class="row mt-3">
                    <div class="col-lg-2 col-2">
                        <div class="py-2 px-3 bg-success rounded d-inline"></div>
                    </div>
                    <div class="col-lg-10 col-10">
                        <h5>Present</h5>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-lg-2 col-2">
                        <div class="py-2 px-3 bg-warning rounded d-inline"></div>
                    </div>
                    <div class="col-lg-10 col-10">
                        <h5>Holiday</h5>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-lg-2 col-2">
                        <div class="py-2 px-3 bg-info rounded d-inline"></div>
                    </div>
                    <div class="col-lg-10 col-10">
                        <h5>Leave</h5>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-lg-2 col-2">
                        <div class="py-2 px-3 bg-primary rounded d-inline"></div>
                    </div>
                    <div class="col-lg-10 col-10">
                        <h5>Late</h5>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-lg-2 col-2">
                        <div class="py-2 px-3 bg-danger rounded d-inline"></div>
                    </div>
                    <div class="col-lg-10 col-10">
                        <h5>Absent</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-1">
            <div class="card-body">
                <div class="border rounded">
                    <div class="row align-items-center rounded">
                        <div class="col-lg-3 col-3 bg-danger text-white py-3 rounded">
                            <h4 class="m-0">{{ count($absentLog) }}</h4>
                        </div>
                        <div class="col-lg-9 col-9">
                            <h4 class="m-0">Total Absentees</h4>
                        </div>
                    </div>
                </div>

                <div class="border rounded mt-3">
                    <div class="row align-items-center rounded">
                        <div class="col-lg-3 col-3 bg-danger text-white py-3 rounded">
                            <h4 class="m-0">{{ count($absentLog) }}</h4>
                        </div>
                        <div class="col-lg-9 col-9">
                            <h4 class="m-0">Monthly Absentees</h4>
                        </div>
                    </div>
                </div>

                <div class="border rounded mt-3">
                    <div class="row align-items-center rounded">
                        <div class="col-lg-3 col-3 bg-success text-white py-3 rounded">
                            <h4 class="m-0">
                                @if (!empty($employee->policy->toArray()))
                                    {{ $employee->policy[0]->pay_roll_settings->off_days_per_month }}
                                @else
                                    0
                                @endif
                            </h4>
                        </div>
                        <div class="col-lg-9 col-9">
                            <h4 class="m-0">
                                Monthly Allowed Leaves</h4>
                        </div>
                    </div>
                </div>

                <div class="border rounded mt-3">
                    <div class="row align-items-center rounded">
                        <div class="col-lg-3 bg-warning text-white py-3 rounded">
                            <h4 class="m-0">8/24</h4>
                        </div>
                        <div class="col-lg-9">
                            <h4 class="m-0">
                                Annual Remaining Leaves</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('modal-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            let attendence = document.getElementById('calenderAttendence').value;
            let events = [];
            if (attendence) {
                attendence = JSON.parse(attendence);
                for (var dateKey in attendence) {
                    if (attendence.hasOwnProperty(dateKey)) {
                        var dateInfo = attendence[dateKey];
                        console.log("Date:", dateKey);
                        console.log("Details:", dateInfo);

                        if (dateInfo.status == 0) {
                            events.push({
                                title: '',
                                start: dateKey,
                                backgroundColor: '#00c5fb' // Background color for a specific event
                            });
                        } else if (dateInfo.status == 1) {
                            events.push({
                                title: '',
                                start: dateKey,
                                backgroundColor: '#55ce63' // Background color for a specific event
                            });
                        } else if (dateInfo.status == 2) {
                            events.push({
                                title: '',
                                start: dateKey,
                                backgroundColor: '#ffbc34' // Background color for a specific event
                            });
                        } else if (dateInfo.status == 3) {
                            events.push({
                                title: '',
                                start: dateKey,
                                backgroundColor: '#f62d51' // Background color for a specific event
                            });
                        } else if (dateInfo.status == 4) {
                            events.push({
                                title: '',
                                start: dateKey,
                                backgroundColor: '#009efb' // Background color for a specific event
                            });
                        }
                        // You can access individual properties like dateInfo.a_date, dateInfo.attendence_visual, etc.
                    }
                }
            }
            console.log(attendence);

            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                height: '620px',
                dateClick: function(info) {
                    // Change the background color of the clicked date cell
                    info.dayEl.style.backgroundColor = 'lightblue';
                },
                events: events,
            });
            calendar.render();
        });
    </script>
@endpush
