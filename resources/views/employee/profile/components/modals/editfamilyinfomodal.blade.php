<!-- Family Info Modal -->
<div id="edit_family_info_modal" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> Family Informations</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" id="edit_family_contact_form">
                    @csrf
                    @method('put')
                    <input type="hidden" name="edit_family_route" id="edit_family_route"
                        value="{{ old('edit_family_route') }}">
                    <input type="hidden" name="family_edit_employee_id"
                        value="{{ old('family_edit_employee_id') ?? $employee->id }}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="family_edit_name" id="family_edit_name"
                                    value="{{ old('family_edit_name') }}">
                                <x-field-validation errorname="family_edit_name" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Relationship <span class="text-danger">*</span></label>
                                <select class="select" name="family_edit_relation">
                                    <option value="">Select</option>
                                    <option value="Brother" @if (old('family_edit_relation') == 'Brother') selected @endif>Brother
                                    </option>
                                    <option value="Sister" @if (old('family_edit_relation') == 'Sister') selected @endif>Sister
                                    </option>
                                    <option value="Spouse" @if (old('family_edit_relation') == 'Spouse') selected @endif>Spouse
                                    </option>
                                    <option value="Mother" @if (old('family_edit_relation') == 'Mother') selected @endif>Mother
                                    </option>
                                    <option value="Father" @if (old('family_edit_relation') == 'Father') selected @endif>Father
                                    </option>
                                </select>
                                <x-field-validation errorname="family_edit_relation" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date of birth <span class="text-danger">*</span></label>
                                <input class="form-control datetimepicker" type="text" name="family_edit_dob"
                                    id="family_edit_dob" value="{{ old('family_edit_dob') }}">
                                <x-field-validation errorname="family_edit_dob" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="family_edit_phone"
                                    id="family_edit_phone" value="{{ old('family_edit_phone') }}">
                                <x-field-validation errorname="family_edit_phone" />
                            </div>
                        </div>
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Family Info Modal -->
