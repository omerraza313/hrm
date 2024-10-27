<!-- Edit Department Modal -->
<div id="edit_department" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Department</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" id="editForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="route" id="route" value="{{ old('route') }}">
                    <div class="form-group">
                        <label class="col-form-label">Department Name <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="edit_name" placeholder="Enter Department Name" id="edit_name"
                            value="{{ old('edit_name') }}" />
                        <x-field-validation errorname="edit_name" />
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Department Modal -->
