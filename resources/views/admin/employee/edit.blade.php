<!-- Edit Employee Modal -->
<div id="edit_employee" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Employee</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="route" id="route" value="{{ old('route') }}">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="profile-img-wrap edit-img">
                                <img class="inline-block" src="{{ asset('assets/img/profiles/avatar-02.jpg') }}"
                                    alt="user" id="edit_image">
                                <div class="fileupload btn">
                                    <span class="btn-text">Edit</span>
                                    <input class="upload" type="file" name="edit_image">
                                    <x-field-validation errorname="edit_image" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">First Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" id="edit_first_name" name="edit_first_name"
                                    value="{{ old('edit_first_name') }}">
                                <x-field-validation errorname="edit_first_name" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Last Name</label>
                                <input class="form-control" type="text" id="edit_last_name" name="edit_last_name"
                                    value="{{ old('edit_last_name') }}">
                                <x-field-validation errorname="edit_last_name" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Blood Group <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="edit_blood_group"
                                    value="{{ old('edit_blood_group') }}" id="edit_blood_group">
                                <x-field-validation errorname="edit_blood_group" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Email <span class="text-danger">*</span></label>
                                <input class="form-control" type="email" id="edit_email" name="edit_email"
                                    value="{{ old('edit_email') }}">
                                <x-field-validation errorname="edit_email" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Password</label>
                                <input class="form-control" type="password" id="edit_password" name="edit_password"
                                    value="{{ old('edit_password') }}">
                                <x-field-validation errorname="edit_password" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label" >Confirm Password</label>
                                <input class="form-control" type="password" id="edit_confirm_password"
                                    name="edit_confirm_password" value="{{ old('edit_confirm_password') }}" >
                                <x-field-validation errorname="edit_confirm_password" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Employee ID <span class="text-danger">*</span></label>
                                <input type="text" id="edit_employee_id" name="edit_employee_id"
                                    value="{{ old('edit_employee_id') }}" readonly class="form-control floating">
                                <x-field-validation errorname="edit_employee_id" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Joining Date <span class="text-danger">*</span></label>
                                <div class="cal-icon"><input class="form-control datetimepicker" type="text"
                                        id="edit_joining_date" name="edit_joining_date"
                                        value="{{ old('edit_joining_date') }}">
                                </div>
                                <x-field-validation errorname="edit_joining_date" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Phone </label>
                                <input class="form-control" type="text" id="edit_phone" name="edit_phone"
                                    value="{{ old('edit_phone') }}">
                                <x-field-validation errorname="edit_phone" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Salary </label>
                                <input class="form-control" type="text" id="edit_salary" name="edit_salary"
                                    value="{{ old('edit_salary') }}">
                                <x-field-validation errorname="edit_salary" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Department <span class="text-danger">*</span></label>
                                <select class="select" id="edit_department" name="edit_department">
                                    <option value="">Select Department</option>
                                    @if (isset($departments) && $departments)
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}"
                                                @if (old('edit_department') == $department->id) selected @endif>
                                                {{ $department->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <x-field-validation errorname="edit_department" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Designation <span class="text-danger">*</span></label>
                                <select class="select form-control" id="edit_designation" name="edit_designation">
                                    <option value="">Select Designation</option>
                                </select>
                                <input type="hidden" id="edit_designation_val"
                                    value="{{ old('edit_designation') }}">
                                <x-field-validation errorname="edit_designation" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Report Manager </label>
                                <select class="select" name="edit_report_manager" id="edit_report_manager">
                                    <option value="">Select Manager</option>
                                    @if (isset($managers) && $managers)
                                        @foreach ($managers as $manager)
                                            <option value="{{ $manager->id }}"
                                                @if (old('edit_report_manager') == $manager->id) selected @endif>
                                                {{ $manager->first_name }} {{ $manager->last_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <x-field-validation errorname="edit_report_manager" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-form-label">Role <span class="text-danger">*</span></label>
                                <select class="select" name="edit_role" id="edit_role">
                                    <option value="1" @if (old('edit_role') == 1) selected @endif>Employee
                                    </option>
                                    <option value="2" @if (old('edit_role') == 2) selected @endif>Manager
                                    </option>
                                </select>
                                <x-field-validation errorname="edit_role" />
                            </div>
                        </div>
                    </div>
                    <div class="submit-section">
                        <button type="submit" class="btn btn-primary submit-btn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Employee Modal -->
