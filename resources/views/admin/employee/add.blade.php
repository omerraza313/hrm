<!-- Add Employee Modal -->
<div id="add_employee" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Employee</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.employee.save') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="profile-img-wrap edit-img">
                                <img class="inline-block" src="{{ asset('assets/img/profiles/avatar-02.jpg') }}"
                                    alt="user">
                                <div class="fileupload btn">
                                    <span class="btn-text">add</span>
                                    <input class="upload" type="file" name="add_image">

                                </div>
                            </div>
                            <div class="text-center">
                                <x-field-validation errorname="add_image" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">First Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="add_first_name"
                                    value="{{ old('add_first_name') }}">
                                <x-field-validation errorname="add_first_name" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Last Name</label>
                                <input class="form-control" type="text" name="add_last_name"
                                    value="{{ old('add_last_name') }}">
                                <x-field-validation errorname="add_last_name" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Pseudo Name<span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="add_pseudo_name"
                                    value="{{ old('add_pseudo_name') }}">
                                <x-field-validation errorname="add_pseudo_name" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">DOB </label>
                                <div class="cal-icon"><input class="form-control datetimepicker" type="text"
                                        name="add_dob" id="add_dob" value="{{ old('add_dob') }}">
                                    <x-field-validation errorname="add_dob" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Blood Group <span class="text-danger">*</span></label>
                                {{-- <input class="form-control" type="text" name="add_blood_group"
                                    value="{{ old('add_blood_group') }}"> --}}
                                <select class="select" name="add_blood_group">
                                    <option value="">Select</option>
                                    <option value="A+" @if (old('add_blood_group') == 'A+') selected @endif>A+
                                    </option>
                                    <option value="A-" @if (old('add_blood_group') == 'A-') selected @endif>A-
                                    </option>
                                    <option value="AB+" @if (old('add_blood_group') == 'AB+') selected @endif>AB+
                                    </option>
                                    <option value="AB-" @if (old('add_blood_group') == 'AB-') selected @endif>AB-
                                    </option>
                                    <option value="B+" @if (old('add_blood_group') == 'B+') selected @endif>B+
                                    </option>
                                    <option value="B-" @if (old('add_blood_group') == 'B-') selected @endif>B-
                                    </option>
                                    <option value="O+" @if (old('add_blood_group') == 'O+') selected @endif>O+
                                    </option>
                                    <option value="O-" @if (old('add_blood_group') == 'O-') selected @endif>O-
                                    </option>
                                </select>
                                <x-field-validation errorname="add_blood_group" />
                                <x-field-validation errorname="add_blood_group" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Email <span class="text-danger">*</span></label>
                                <input class="form-control" type="email" name="add_email"
                                    value="{{ old('add_email') }}">
                                <x-field-validation errorname="add_email" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Password</label>
                                <input class="form-control" type="password" name="add_password"
                                    value="{{ old('add_password') }}">
                                <x-field-validation errorname="add_password" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Confirm Password</label>
                                <input class="form-control" type="password" name="add_confirm_password"
                                    value="{{ old('add_confirm_password') }}">
                                <x-field-validation errorname="add_confirm_password" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Gender </label>
                                <select class="select" name="add_gender">
                                    <option value="">Select</option>
                                    <option value="Male" @if (old('add_gender') == 'Male') selected @endif>Male
                                    </option>
                                    <option value="Female" @if (old('add_gender') == 'Female') selected @endif>Female
                                    </option>
                                </select>
                                <x-field-validation errorname="add_gender" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Joining Date <span class="text-danger">*</span></label>
                                <div class="cal-icon"><input class="form-control datetimepicker" type="text"
                                        name="add_join_date" id="add_join_date" value="{{ old('add_join_date') }}"></div>
                                <x-field-validation errorname="add_join_date" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Phone </label>
                                <input class="form-control" type="text" name="add_phone"
                                    value="{{ old('add_phone') }}">
                                <x-field-validation errorname="add_phone" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Salary </label>
                                <input class="form-control" type="text" name="add_salary"
                                    value="{{ old('add_salary') }}">
                                <x-field-validation errorname="add_salary" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Martial Status </label>
                                <select class="select" name="add_martial_status">
                                    <option value="">Select</option>
                                    <option value="Single" @if (old('add_martial_status') == 'Single') selected @endif>Single
                                    </option>
                                    <option value="Married" @if (old('add_martial_status') == 'Married') selected @endif>Married
                                    </option>
                                </select>
                                <x-field-validation errorname="add_martial_status" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Emergency Contact Name <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="add_em_contact_name"
                                    value="{{ old('add_em_contact_name') }}">
                                <x-field-validation errorname="add_em_contact_name" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Emergency Contact Number </label>
                                <input class="form-control" type="text" name="add_em_contact_num"
                                    value="{{ old('add_em_contact_num') }}">
                                <x-field-validation errorname="add_em_contact_num" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Emergency Contact Relation </label>
                                <select class="select" name="add_em_contact_relation">
                                    <option value="">Select</option>
                                    <option value="Brother" @if (old('add_em_contact_relation') == 'Brother') selected @endif>Brother
                                    </option>
                                    <option value="Sister" @if (old('add_em_contact_relation') == 'Sister') selected @endif>Sister
                                    </option>
                                    <option value="Spouse" @if (old('add_em_contact_relation') == 'Spouse') selected @endif>Spouse
                                    </option>
                                    <option value="Mother" @if (old('add_em_contact_relation') == 'Mother') selected @endif>Mother
                                    </option>
                                    <option value="Father" @if (old('add_em_contact_relation') == 'Father') selected @endif>Father
                                    </option>
                                </select>
                                <x-field-validation errorname="add_em_contact_relation" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">CNIC </label>
                                <input class="form-control" type="text" name="add_cnic"
                                    value="{{ old('add_cnic') }}">
                                <x-field-validation errorname="add_cnic" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Report Manager </label>
                                <select class="select" name="add_report_manager" id="add_report_manager">
                                    <option value="">Select Manager</option>
                                    @if (isset($managers) && $managers)
                                        @foreach ($managers as $manager)
                                            <option value="{{ $manager->id }}"
                                                @if (old('add_report_manager') == $manager->id) selected @endif>
                                                {{ $manager->first_name }} {{ $manager->last_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <x-field-validation errorname="add_report_manager" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Department <span class="text-danger">*</span></label>
                                <select class="select" name="add_department" id="add_department">
                                    <option value="">Select Department</option>
                                    @if (isset($departments) && $departments)
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}"
                                                @if (old('add_department') == $department->id) selected @endif>
                                                {{ $department->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <x-field-validation errorname="add_department" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Designation <span class="text-danger">*</span></label>
                                <select class="select" name="add_designation" id="add_designation">
                                    <option>Select Designation</option>
                                </select>
                                <x-field-validation errorname="add_designation" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Role <span class="text-danger">*</span></label>
                                <select class="select" name="add_role">
                                    <option value="1" @if (old('add_role') == 1) selected @endif>Employee
                                    </option>
                                    <option value="2" @if (old('add_role') == 2) selected @endif>Manager
                                    </option>
                                </select>
                                <x-field-validation errorname="add_role" />
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-form-label">Permanent Address <span
                                        class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="add_per_address"
                                    value="{{ old('add_per_address') }}">
                                <x-field-validation errorname="add_per_address" />
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">City <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="add_per_city"
                                    value="{{ old('add_per_city') }}">
                                <x-field-validation errorname="add_per_city" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">State </label>
                                <input class="form-control" type="text" name="add_per_state"
                                    value="{{ old('add_per_state') }}">
                                <x-field-validation errorname="add_per_state" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Zip <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="add_per_zip"
                                    value="{{ old('add_per_zip') }}">
                                <x-field-validation errorname="add_per_zip" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Country</label>
                                <input class="form-control" type="text" name="add_per_country"
                                    value="{{ old('add_per_country') }}">
                                <x-field-validation errorname="add_per_country" />
                                <input type="hidden" id="add_curr_status_id" name="add_curr_status"
                                    value="{{ old('add_curr_status') ?? 'false' }}">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="Current_Address">
                                    <input type="checkbox" id="Current_Address"
                                        value="{{ old('add_curr_status') ?? 'false' }}"
                                        {{ old('add_curr_status') ? (old('add_curr_status') == 'true' ? 'checked' : '') : '' }} />
                                    Same as permanent address
                                </label>
                            </div>
                        </div>

                        <div class="col-sm-12" id="current_address_fields"
                            style="display: {{ old('add_curr_status') ? (old('add_curr_status') == 'true' ? 'none' : 'block') : 'block' }}">
                            <div class="row">
                                <div class="col-sm-12">
                                    <label class="col-form-label">Current Address<span
                                            class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="add_curr_address"
                                        value="{{ old('add_curr_address') }}">
                                    <x-field-validation errorname="add_curr_address" />
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="col-form-label">City <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="add_curr_city"
                                            value="{{ old('add_curr_city') }}">
                                        <x-field-validation errorname="add_curr_city" />
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="col-form-label">State</label>
                                        <input class="form-control" type="text" name="add_curr_state"
                                            value="{{ old('add_curr_state') }}">
                                        <x-field-validation errorname="add_curr_state" />
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Zip <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="add_curr_zip"
                                            value="{{ old('add_curr_zip') }}">
                                        <x-field-validation errorname="add_curr_zip" />
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Country</label>
                                        <input class="form-control" type="text" name="add_curr_country"
                                            value="{{ old('add_curr_country') }}">
                                        <x-field-validation errorname="add_curr_country" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@php
    $add_modal_status = false;
    foreach ($errors->getMessages() as $field => $messages) {
        if (substr($field, 0, 4) == 'add_') {
            $add_modal_status = true;
        }
    }
@endphp
@if ($add_modal_status)
    @push('modal-script')
        <script>
            $(document).ready(function() {
                $("#add_employee").modal("show");
            });
        </script>
    @endpush
@endif
@push('modal-script')
    <script>
        var checkbox = document.getElementById("Current_Address");
        var checkbox_fields = document.getElementById("current_address_fields");
        var add_curr_status_id = document.getElementById("add_curr_status_id");

        // Add an event listener for the "change" event
        checkbox.addEventListener("change", function() {
            // Check if the checkbox is checked
            if (checkbox.checked) {
                // If checked, change the value to "true"
                checkbox.value = "true";
                add_curr_status_id.value = "true";
                checkbox_fields.style.display = "none";

            } else {
                // If unchecked, change the value to "false"
                checkbox.value = "false";
                add_curr_status_id.value = "false";
                checkbox_fields.style.display = "block";
            }
        });

    </script>
@endpush
<!-- /Add Employee Modal -->
