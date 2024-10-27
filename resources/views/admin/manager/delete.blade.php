<!-- Delete Employee Modal -->
<div class="modal custom-modal fade" id="delete_employee" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="form-header">
                    <h3>Delete Employee</h3>
                    <p>Are you sure want to delete?</p>
                </div>
                <div class="modal-btn delete-action">
                    <form method="post" id="deleteForm">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="route_name" id="mainRoute" value="">
                        <div class="row">
                            <div class="col-lg-12">
                                <label for="" class="form-label">Reason</label>
                                <textarea name="delete_note" class="form-control mb-4" id="" cols="30" rows="4">{{ old('delete_note') }}</textarea>
                                <x-field-validation errorname="delete_note" />
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label for="" class="form-label">All Cleared?</label>
                                    <select class="form-select form-select-lg" name="delete_clearance" id="">
                                        <option value="no" @if (old('delete_clearance') == 'no') selected @endif>No
                                        </option>
                                        <option value="yes" @if (old('delete_clearance') == 'yes') selected @endif>Yes
                                        </option>
                                    </select>
                                    <x-field-validation errorname="delete_clearance" />
                                </div>
                            </div>
                            <div class="col-6">
                                <button type="submit" class="btn btn-primary continue-btn w-100">Delete</button>
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
