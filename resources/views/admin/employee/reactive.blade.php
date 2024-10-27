<!-- Delete Employee Modal -->
<div class="modal custom-modal fade" id="reactive_employee" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header">
                    <h3>Reactivate Employee</h3>
                    <p>Are you sure to reactive the employee?</p>
                </div>
                <div class="modal-btn delete-action">
                    <form method="post" id="reactiveForm">
                        @csrf
                        @method('POST')
                        <input type="hidden" name="route_name" id="mainRoute" value="">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="col-form-label">Rejoining Date <span
                                            class="text-danger">*</span></label>
                                    <div class="cal-icon"><input class="form-control datetimepicker" type="text"
                                            name="reactive_rejoining" value="{{ old('reactive_rejoining') }}"></div>
                                    <x-field-validation errorname="reactive_rejoining" />
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="col-form-label">Probation Period </label>
                                    <input class="form-control" type="text" name="reactive_probation_period"
                                        value="{{ old('reactive_probation_period') }}">
                                    <x-field-validation errorname="reactive_probation_period" />
                                </div>
                            </div>
                            <div class="col-6">
                                <button type="submit" class="btn btn-primary continue-btn w-100">Reactive</button>
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
