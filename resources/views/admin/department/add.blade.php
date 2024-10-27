<!-- Add Department Modal -->
<div id="add_department" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Department</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.department.save') }}" method="post">
                    @csrf
                    @method('POST')
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-form-label">Department Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="add_name"
                                    placeholder="Enter Department Name" value="{{ old('add_name') }}" />
                                <x-field-validation errorname="add_name" />
                            </div>
                        </div>
                        {{-- <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-form-label">Department Description </label>
                                <input class="form-control" type="text">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-form-label">Assign Manager </label>
                                <input class="form-control" type="text">
                            </div>
                        </div> --}}


                        <div class="submit-section">
                            <button class="btn btn-primary submit-btn">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Add Department Modal -->

@if ($errors->has('add_name'))
    @push('modal-script')
        <script>
            $(document).ready(function() {
                // Show the modal on page load
                $("#add_department").modal('show');
            });
        </script>
    @endpush
@endif
