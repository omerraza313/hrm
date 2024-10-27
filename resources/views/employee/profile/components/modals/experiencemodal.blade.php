<!-- Experience Modal -->
<div id="experience_info" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Experience Informations</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('profile.store_experience') }}" method="POST">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="employee_id" value="{{ old('employee_id') ?? $employee->id }}" />
                    <div class="form-scroll">
                        <div id="clone_exp_field">
                            @if (count(old('exp_company_name', [''])) > 0)
                                @for ($i = 0; $i < count(old('exp_company_name', [''])); $i++)
                                    @if (count(old('exp_company_name', [''])) < 2)
                                        @foreach ($employee->experiences as $experience)
                                            <div class="card">
                                                <div class="card-body">
                                                    <h3 class="card-title">Experience Information <a
                                                            href="javascript:void(0);" onclick="deleteExpClone(event);"
                                                            class="delete-icon"><i class="fa fa-trash-o"></i></a></h3>
                                                    <input type="hidden" name="exp_id[]"
                                                        value="{{ old('exp_id.' . $loop->index) ?? $experience->id }}">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Company Name</label>
                                                                <input type="text" class="form-control"
                                                                    name="exp_company_name[]"
                                                                    value="{{ old('exp_company_name.' . $loop->index) ?? $experience->company_name }}">
                                                                <x-field-validation :errorname="'exp_company_name.' . $loop->index" />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Location</label>
                                                                <input type="text" class="form-control"
                                                                    name="exp_location[]"
                                                                    value="{{ old('exp_location.' . $loop->index) ?? $experience->location }}">
                                                                <x-field-validation :errorname="'exp_location.' . $loop->index" />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Period From</label>
                                                                <div class="cal-icon">
                                                                    <input type="text"
                                                                        class="form-control datetimepicker"
                                                                        name="exp_period_from[]"
                                                                        value="{{ old('exp_period_from.' . $loop->index) ?? $experience->from_date }}">
                                                                    <x-field-validation :errorname="'exp_period_from.' . $loop->index" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Period To</label>
                                                                <div class="cal-icon">
                                                                    <input type="text"
                                                                        class="form-control datetimepicker"
                                                                        name="exp_period_to[]"
                                                                        value="{{ old('exp_period_to.' . $loop->index) ?? $experience->to_date }}">
                                                                    <x-field-validation :errorname="'exp_period_to.' . $loop->index" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Job Position</label>
                                                                <input type="text" class="form-control"
                                                                    name="exp_job_position[]"
                                                                    value="{{ old('exp_job_position.' . $loop->index) ?? $experience->job_position }}">
                                                                <x-field-validation :errorname="'exp_job_position.' . $loop->index" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="card" id="exp_{{ $i + 1 }}">
                                        <div class="card-body">
                                            <h3 class="card-title">Experience Information <a href="javascript:void(0);"
                                                    onclick="deleteExpClone(event);" class="delete-icon"><i
                                                        class="fa fa-trash-o"></i></a></h3>
                                            <input type="hidden" name="exp_id[]" value="{{ old('exp_id.' . $i) }}">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Company Name</label>
                                                        <input type="text" class="form-control" name="exp_company_name[]"
                                                            value="{{ old('exp_company_name.' . $i) }}">
                                                        <x-field-validation :errorname="'exp_company_name.' . $i" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Location</label>
                                                        <input type="text" class="form-control" name="exp_location[]"
                                                            value="{{ old('exp_location.' . $i) }}">
                                                        <x-field-validation :errorname="'exp_location.' . $i" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Period From</label>
                                                        <div class="cal-icon">
                                                            <input type="text" class="form-control datetimepicker"
                                                                name="exp_period_from[]"
                                                                value="{{ old('exp_period_from.' . $i) }}">
                                                        </div>
                                                        <x-field-validation :errorname="'exp_period_from.' . $i" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Period To</label>
                                                        <div class="cal-icon">
                                                            <input type="text" class="form-control datetimepicker"
                                                                name="exp_period_to[]"
                                                                value="{{ old('exp_period_to.' . $i) }}">
                                                        </div>
                                                        <x-field-validation :errorname="'exp_period_to.' . $i" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Job Position</label>
                                                        <input type="text" class="form-control"
                                                            name="exp_job_position[]"
                                                            value="{{ old('exp_job_position.' . $i) }}">
                                                        <x-field-validation :errorname="'exp_job_position.' . $i" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            @endif




                        </div>
                        <div class="add-more">
                            <a href="javascript:void(0);" onclick="add_experience_clone();"><i
                                    class="fa fa-plus-circle"></i> Add More</a>
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
<!-- /Experience Modal -->


@push('modal-script')
    <script src="{{ asset('assets/js/profile/experience.js') }}"></script>
@endpush

@php
    $experience_error_status = false;
    foreach ($errors->getMessages() as $field => $messages) {
        if (substr($field, 0, 4) == 'exp_') {
            $experience_error_status = true;
            break;
        }
    }
@endphp

@if ($experience_error_status)
    @push('modal-script')
        <script>
            $(document).ready(function() {
                // Code to run when the page is loaded

                $("#experience_info").modal("show");
            });
        </script>
    @endpush
@endif
