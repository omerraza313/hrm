<!-- Delete Employee Modal -->
<div class="modal custom-modal fade" id="delete_employee" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header">
                    <h3>Deactivate Employee</h3>
                    <p>Are you sure to deactivate the employee?</p>
                </div>
                <div class="modal-btn delete-action">
                    <form method="post" id="deleteForm">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="route_name" id="mainRoute" value="">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="" class="form-label">Notice Period Served?</label>
                                    <select class="select" name="delete_notice_period_served" id="">
                                        <option value="0" @if (old('delete_notice_period_served') == '0') selected @endif>No
                                        </option>
                                        <option value="1" @if (old('delete_notice_period_served') == '1') selected @endif>Yes
                                        </option>
                                    </select>
                                    <x-field-validation errorname="delete_notice_period_served" />
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="col-form-label">Notice Period Date <span
                                            class="text-danger">*</span></label>
                                    <div class="cal-icon"><input class="form-control datetimepicker" type="text"
                                            name="delete_notice_period_date"
                                            value="{{ old('delete_notice_period_date') }}"></div>
                                    <x-field-validation errorname="delete_notice_period_date" />
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="col-form-label">Notice Period Duration </label>
                                    <input class="form-control" type="text" name="delete_notice_period_duration"
                                        value="{{ old('delete_notice_period_duration') }}">
                                    <x-field-validation errorname="delete_notice_period_duration" />
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="col-form-label">Exit Date <span class="text-danger">*</span></label>
                                    <div class="cal-icon"><input class="form-control datetimepicker" type="text"
                                            name="delete_exit_date" value="{{ old('delete_exit_date') }}"></div>
                                    <x-field-validation errorname="delete_exit_date" />
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="" class="form-label">All Cleared?</label>
                                    <select class="select" name="delete_clearance" id="">
                                        <option value="no" @if (old('delete_clearance') == 'no') selected @endif>No
                                        </option>
                                        <option value="yes" @if (old('delete_clearance') == 'yes') selected @endif>Yes
                                        </option>
                                    </select>
                                    <x-field-validation errorname="delete_clearance" />
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="" class="form-label">Reason</label>
                                    <select class="select" name="delete_reason" id="">
                                        @foreach (\App\Helpers\EmployeeHelper::get_reasons() as $key => $reason)
                                            <option value="{{ $key }}"
                                                @if (old('delete_reason') == '{{ $key }}') selected @endif>
                                                {{ $reason }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-field-validation errorname="delete_reason" />
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <label for="" class="form-label">Comments</label>
                                <textarea name="delete_note" class="form-control mb-4" id="" cols="30" rows="4">{{ old('delete_note') }}</textarea>
                                <x-field-validation errorname="delete_note" />
                            </div>
                            <div class="col-6">
                                <button type="submit" class="btn btn-primary continue-btn w-100">Deactive</button>
                            </div>
                            <div class="col-6">
                                <a href="javascript:void(0);" data-bs-dismiss="modal"
                                    class="btn btn-primary cancel-btn">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Delete Employee Modal -->
