<!-- Emergency Contact Modal -->
<div id="emergency_contact_modal" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Primary Contact</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('emergency.contact') }}">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="eme_employee_id" value="{{ $employee->id }}">
                    <input type="hidden" name="eme_id" value="{{ $employee->emergency_contacts->id }}" />
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="eme_name"
                                    value="{{ old('eme_name') ?? $employee->emergency_contacts->name }}">
                                <x-field-validation errorname="eme_name" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Relationship <span class="text-danger">*</span></label>
                                <select class="select" name="eme_relation">
                                    <option value="">Select</option>
                                    <option value="Brother" @if ((old('eme_relation') ?? $employee->emergency_contacts->relation) == 'Brother') selected @endif>Brother
                                    </option>
                                    <option value="Sister" @if ((old('eme_relation') ?? $employee->emergency_contacts->relation) == 'Sister') selected @endif>Sister
                                    </option>
                                    <option value="Spouse" @if ((old('eme_relation') ?? $employee->emergency_contacts->relation) == 'Spouse') selected @endif>Spouse
                                    </option>
                                    <option value="Mother" @if ((old('eme_relation') ?? $employee->emergency_contacts->relation) == 'Mother') selected @endif>Mother
                                    </option>
                                    <option value="Father" @if ((old('eme_relation') ?? $employee->emergency_contacts->relation) == 'Father') selected @endif>Father
                                    </option>
                                </select>
                                <x-field-validation errorname="eme_relation" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Phone <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="eme_number"
                                    value="{{ old('eme_number') ?? $employee->emergency_contacts->number }}">
                                <x-field-validation errorname="eme_number" />
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
<!-- /Emergency Contact Modal -->
