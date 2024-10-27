<!-- Personal Info Modal -->
<div id="personal_info_modal" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Personal Information</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('personal.info') }}" method="POST">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="pinfo_employee_id"
                        value="{{ old('pinfo_employee_id') ?? $employee->id }}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Pseudo Name</label>
                                <input type="text" class="form-control" name="pinfo_pseudo_name"
                                    value="{{ old('pinfo_pseudo_name') ?? $employee->employee_details->pseudo_name }}">
                                <x-field-validation errorname="pinfo_pseudo_name" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>CNIC</label>
                                <input class="form-control" type="text" name="pinfo_cnic"
                                    value="{{ old('pinfo_cnic') ?? $employee->employee_details->cnic }}">
                                <x-field-validation errorname="pinfo_cnic" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone</label>
                                <input class="form-control" type="text" name="pinfo_phone"
                                    value="{{ old('pinfo_phone') ?? $employee->employee_details->phone }}">
                                <x-field-validation errorname="pinfo_phone" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Marital Status <span class="text-danger">*</span></label>
                                <select class="select" name="pinfo_marital_status">
                                    <option value="">Select</option>
                                    <option value="Single" @if ((old('pinfo_marital_status') ?? $employee->employee_details->martial_status) == 'Single') selected @endif>Single
                                    </option>
                                    <option value="Married" @if ((old('pinfo_marital_status') ?? $employee->employee_details->martial_status) == 'Married') selected @endif>Married
                                    </option>
                                </select>
                                <x-field-validation errorname="pinfo_marital_status" />
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
<!-- /Personal Info Modal -->
