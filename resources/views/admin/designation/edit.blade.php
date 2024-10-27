<!-- Edit Designation Modal -->
<div id="edit_designation" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Designation</h5>
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
                        <label>Designation Name  <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="edit_name" id="edit_name"
                            placeholder="Please enter designation name" value="{{ old('edit_name') }}" />
                        <x-field-validation errorname="edit_name" />
                    </div>
                    <div class="form-group">
                        <label>Department <span class="text-danger">*</span></label>
                        <select class="select" name="edit_department" id="edit_department">
                            <option value="">Select Department</option>
                            @php
                                $edit_value = old('edit_id')
                            @endphp
                            @if (isset($departments) && !empty($departments))
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}"
                                        @if (old('edit_department') == $department->id) selected @endif>{{ $department->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <x-field-validation errorname="edit_department" />
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Designation Modal -->
