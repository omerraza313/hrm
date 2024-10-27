<!-- Add Leave Modal -->
<div id="add_leave" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Leave</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="alertmessage">

                </div>
                <form method="POST" action="{{ route('employee.leave.apply') }}" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <div class="form-group">
                        <label>Subject <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="add_leave_subject"
                            value="{{ old('add_leave_subject') }}" placeholder="Please enter your subject">
                        <x-field-validation errorname="add_leave_subject" />
                    </div>
                    <div class="form-group">
                        <label>Application Body <span class="text-danger">*</span></label>
                        <textarea rows="4" name="add_leave_body" class="form-control" placeholder="Please enter your reason">{{ old('add_leave_body') }}</textarea>
                        <x-field-validation errorname="add_leave_body" />
                    </div>
                    <div class="form-group">
                        <label>Leave From <span class="text-danger">*</span></label>
                        <div class="cal-icon">
                            <input class="form-control datetimepicker" type="text" name="add_leave_from"
                                id="add_leave_from" value="{{ old('add_leave_from') }}">
                            <x-field-validation errorname="add_leave_from" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label>To <span class="text-danger">*</span></label>
                        <div class="cal-icon">
                            <input class="form-control datetimepicker" type="text" name="add_leave_to"
                                id="add_leave_to" value="{{ old('add_leave_to') }}">
                            <x-field-validation errorname="add_leave_to" />
                        </div>
                    </div>
                    <button class="btn btn-primary btn-sm" type="button"
                        onclick="adjustleavedates(event);">Adjust</button>
                    <div id="adjustdates">
                        @if ($errors->any())
                            @if (count(old('add_leave_adjust_date', [''])) > 0)
                                @for ($i = 0; $i < count(old('add_leave_adjust_date', [''])); $i++)
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Date <span class="text-danger">*</span></label>
                                                <div class="cal-icon">
                                                    <input class="form-control" type="text"
                                                        name="add_leave_adjust_date[]" id="add_leave_adjust_date"
                                                        value="{{ old('add_leave_adjust_date.' . $i) }}" readOnly>
                                                    <x-field-validation :errorname="'add_leave_adjust_date.' . $i" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Leave Type <span class="text-danger">*</span></label>
                                                <select class="form-control" name="add_leave_adjust_plan[]"
                                                    onchange="adjustLeaveHF(event, {{ $i }})">
                                                    <option value="">--Select--</option>
                                                    @foreach ($leaves as $leave)
                                                        <option value="{{ $leave->id }}"
                                                            @if ($leave->id == old('add_leave_adjust_plan.' . $i)) selected @endif
                                                            data-unit="{{ $leave->unit->id }}">
                                                            {{ $leave->title }} ({{ $leave->unit->name }})</option>
                                                    @endforeach
                                                </select>
                                                <x-field-validation :errorname="'add_leave_adjust_plan.' . $i" />
                                            </div>
                                        </div>
                                        <div class="col-lg-12" id="hour_field_{{ $i }}"
                                            style="display: none;">
                                            <div class="form-group">
                                                <label>Hours <span class="text-danger">*</span></label>

                                                <input class="form-control" type="number"
                                                    name="add_leave_adjust_hour[]" id="add_leave_adjust_hour"
                                                    value="{{ old('add_leave_adjust_hour.' . $i) ?? 0 }}"
                                                    step="1">
                                                {{-- <x-field-validation :errorname="'add_leave_adjust_hour.' . $i" /> --}}
                                            </div>
                                        </div>
                                        <div class="col-lg-12" id="half_day_field_{{ $i }}"
                                            style="display: none;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    name="add_leave_half_day[]" value="true" id="half_day_leave">
                                                <label class="form-check-label" for="half_day_leave">
                                                    Half Day
                                                </label>

                                            </div>
                                        </div>
                                        <x-field-validation :errorname="'half_day_leave.' . $i" />
                                        <x-field-validation :errorname="'add_leave_adjust_hour.' . $i" />
                                    </div>
                                @endfor
                            @endif
                        @endif
                    </div>
                    <div class="form-group mt-3">
                        <label>Upload Document</label>
                        <input type="file" class="form-control" name="add_leave_document">
                        <x-field-validation errorname="add_leave_document" />
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Add Leave Modal -->
@php
    $add_leave_error_status = false;
    foreach ($errors->getMessages() as $field => $messages) {
        if (substr($field, 0, 10) == 'add_leave_') {
            $add_leave_error_status = true;
            break;
        }
    }
@endphp

@if ($add_leave_error_status)
    @push('modal-script')
        <script>
            $(document).ready(function() {
                // Code to run when the page is loaded

                $("#add_leave").modal("show");
            });
        </script>
    @endpush
@endif
@push('modal-script')
    <script>
        $(document).ready(function() {
            $('.datetimepicker').datetimepicker();
            // Add onchange event
            $('.datetimepicker').on('dp.change', function() {
                // Call your function here
                emptyAdjust();
            });
        });




        // Get Leave Plans Globlly
        var leavePlans = [];
        $(window).on('load', function() {
            // Make AJAX call when the window is loaded
            $.ajax({
                url: '{{ route('employee.leave.plans.values') }}', // Replace with your API endpoint
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Handle the successful response
                    console.log('Data:', data);
                    leavePlans = [...data.data];
                    if ('{{ $add_leave_error_status }}' == 'true') {

                        $("#add_leave").modal("show");

                    }
                    // You can update your HTML with the received data here
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    console.error('Error:', error);
                }
            });
        });


        function emptyAdjust() {
            let adjustdates = document.getElementById('adjustdates');
            adjustdates.innerHTML = '';
        }


        function adjustleavedates(ev) {
            console.clear();
            let fromDateValue = document.getElementById('add_leave_from').value;
            let toDateValue = document.getElementById('add_leave_to').value;

            let fromDate = parseDate(fromDateValue);
            let toDate = parseDate(toDateValue);

            if (toDate >= fromDate) {
                message('success', '');
                let timeDiff = toDate - fromDate;
                let daysDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
                let adjustdates = document.getElementById('adjustdates');
                adjustdates.innerHTML = '';
                for (let index = 0; index <= daysDiff; index++) {
                    let currentDate = new Date(fromDate);
                    currentDate.setDate(fromDate.getDate() + index);
                    let options = { year: 'numeric', month: '2-digit', day: '2-digit'};

                    let cDate = currentDate.toLocaleDateString('en-US', options);
                    // let cDate = currentDate.toLocaleDateString('en-US');
                    // var cDate2 = cDate.split("/");
                    // alert(toDate+' - '+fromDate);
                    adjustdates.innerHTML += `
                    <div class="row" id="total_leave_${index}">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Date <span class="text-danger">*</span></label>
                                <div class="cal-icon">
                                    <input class="form-control" type="text" name="add_leave_adjust_date[]"
                                        id="add_leave_adjust_date" value="${cDate}" readOnly>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Leave Type <span class="text-danger">*</span></label>
                                <select class="form-control" name="add_leave_adjust_plan[]" onchange="adjustLeaveHF(event, ${index})">
                                    <option value="">--Select--</option>
                                    ${get_leave_plans_options()}
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-12" id="hour_field_${index}" style="display: none;">
                            <div class="form-group">
                                <label>Hours <span class="text-danger">*</span></label>

                                    <input class="form-control" type="number" name="add_leave_adjust_hour[]"
                                        id="add_leave_adjust_hour" value="0" step="1">

                            </div>
                        </div>
                        <div class="col-lg-12" id="half_day_field_${index}" style="display: none;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="true" name="add_leave_half_day[]" id="half_day_leave">
                                <label class="form-check-label" for="half_day_leave">
                                    Half Day
                                </label>
                            </div>
                        </div>
                    </div>
                    `;
                }
            } else {
                message('danger', 'Invalid date range. To date must be greater than or equal to from date.');
            }

        }

        function get_leave_plans_options() {
            let options = '';
            console.clear();
            leavePlans.forEach(element => {
                options += `
                <option value="${element.id}" data-unit="${element.unit.id}">${element.title} (${element.unit.name})</option>
                `;
            });

            return options;
        }

        function parseDate(dateString) {
            // Parse the date string in DD/MM/YYYY format - old
            // Parse the date string in DD/MM/YYYY format
            var parts = dateString.split('/');
            return new Date(parts[2], parts[0] - 1, parts[1]);
        }

        function message(code, message) {
            let alertmessage = document.getElementById('alertmessage');
            console.log('working');
            if (message == '') {
                alertmessage.innerHTML = ``;
            } else {
                alertmessage.innerHTML = `
                <div class="alert alert-${code} alert-dismissible fade show" role="alert">
                    <strong>${capitalizeFirstLetterOnly(code)}: </strong> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    </button>
                </div>
                `;
            }
        }

        function capitalizeFirstLetterOnly(str) {
            // Capitalize only the first letter of the string
            if (str == 'danger') {
                str = 'error';
            }
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        function adjustLeaveHF(ev, index) {
            let optionElement = ev.target;
            let selectedOption = optionElement.options[optionElement.selectedIndex];
            console.log(selectedOption);
            let unit = selectedOption.dataset.unit
            if (unit == '2') {
                document.getElementById(`hour_field_${index}`).style.display = 'block';
                document.getElementById(`half_day_field_${index}`).style.display = 'none';
            } else {
                document.getElementById(`hour_field_${index}`).style.display = 'none';
                document.getElementById(`half_day_field_${index}`).style.display = 'block';
            }

        }
    </script>
@endpush
