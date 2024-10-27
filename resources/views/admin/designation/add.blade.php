<!-- Add Designation Modal -->
<div id="add_designation" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Designation</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.designation.save') }}" method="post">
                    @csrf
                    @method('POST')
                    <div class="form-group">
                        <label>Designation Name  <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="add_name"
                            placeholder="Please enter designation name" value="{{ old('add_name') }}" />
                        <x-field-validation errorname="add_name" />
                    </div>
                    <div class="form-group">
                        <label>Department <span class="text-danger">*</span></label>
                        <select class="select" name="add_department">
                            <option value="">Select Department</option>
                            @if (isset($departments) && !empty($departments))
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}"
                                        @if (old('add_department') == $department->id) selected @endif>{{ $department->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <x-field-validation errorname="add_department" />
                    </div>
                    <div class="submit-section">
                        <button class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Add Designation Modal -->

@if ($errors->has('add_name') || $errors->has('add_department'))
    @push('modal-script')
        <script>
            $(document).ready(function() {
                // Show the modal on page load
                $("#add_designation").modal('show');
            });
        </script>
    @endpush
@endif
