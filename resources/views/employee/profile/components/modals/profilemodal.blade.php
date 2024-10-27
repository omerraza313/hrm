<!-- Profile Modal -->
<div id="profile_info" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Profile Information</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('profile.info') }}" method="POST">
                    @csrf
                    @method('POST')
                    <div class="row">
                        <div class="col-md-12">
                            {{-- <div class="profile-img-wrap edit-img">
                                <img class="inline-block" src="assets/img/profiles/avatar-02.jpg" alt="user">
                                <div class="fileupload btn">
                                    <span class="btn-text">edit</span>
                                    <input class="upload" type="file">
                                </div>
                            </div> --}}
                            <input type="hidden" name="pro_user_id" value="{{ old('pro_user_id') ?? $employee->id }}">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>First Name</label>
                                        <input type="text" class="form-control" name="pro_first_name"
                                            value="{{ old('pro_first_name') ?? $employee->first_name }}">
                                        <x-field-validation errorname="pro_first_name" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Last Name</label>
                                        <input type="text" class="form-control" name="pro_last_name"
                                            value="{{ old('pro_last_name') ?? $employee->last_name }}">
                                        <x-field-validation errorname="pro_last_name" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Birth Date</label>
                                        <div class="cal-icon">
                                            <input class="form-control datetimepicker" type="text" name="pro_dob"
                                                value="{{ old('pro_dob') ?? $employee->employee_details->dob }}">
                                            <x-field-validation errorname="pro_dob" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Gender</label>
                                        <select class="select form-control" name="pro_gender">
                                            <option>--Select--</option>
                                            <option value="Male" @if ($employee->employee_details->gender == 'Male') selected @endif>
                                                Male</option>
                                            <option value="Female" @if ($employee->employee_details->gender == 'Female') selected @endif>
                                                Female</option>
                                        </select>
                                        <x-field-validation errorname="pro_gender" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <input type="hidden" name="pro_address_id"
                            value="{{ old('pro_address_id') ?? $employee->address[0]->id }}">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Address</label>
                                <input type="text" class="form-control" name="pro_address"
                                    value="{{ old('pro_address') ?? $employee->address[0]->address }}">
                                <x-field-validation errorname="pro_address" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>State</label>
                                <input type="text" class="form-control" name="pro_state"
                                    value="{{ old('pro_state') ?? $employee->address[0]->state }}">
                                <x-field-validation errorname="pro_state" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>City</label>
                                <input type="text" class="form-control" name="pro_city"
                                    value="{{ old('pro_city') ?? $employee->address[0]->city }}">
                                <x-field-validation errorname="pro_city" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Country</label>
                                <input type="text" class="form-control" name="pro_country"
                                    value="{{ old('pro_country') ?? $employee->address[0]->country }}">
                                <x-field-validation errorname="pro_country" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Pin Code</label>
                                <input type="text" class="form-control" name="pro_zip"
                                    value="{{ old('pro_zip') ?? $employee->address[0]->zip }}">
                                <x-field-validation errorname="pro_zip" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="text" class="form-control" name="pro_number"
                                    value="{{ old('pro_number') ?? $employee->employee_details->phone }}">
                                <x-field-validation errorname="pro_number" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Department <span class="text-danger">*</span></label>
                                <select class="select" name="pro_department" id="pro_department">
                                    <option value="">Select Department</option>
                                    @if (isset($departments) && $departments)
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}"
                                                @if ((old('pro_department') ?? $employee->employee_details->department_id) == $department->id) selected @endif>
                                                {{ $department->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <x-field-validation errorname="pro_department" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Designation <span class="text-danger">*</span></label>
                                <select class="select" name="pro_designation" id="pro_designation">
                                    <option>Select Designation</option>
                                </select>
                                <x-field-validation errorname="pro_designation" />
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
<!-- /Profile Modal -->
