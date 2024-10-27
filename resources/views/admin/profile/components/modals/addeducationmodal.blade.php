<!-- Education Modal -->
<input type="hidden" id="subject_list" value="{{ json_encode(\App\Helpers\ProfileHelper::get_education_subject()) }}">
<div id="education_info" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> Education Informations</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('profile.store_education') }}" method="POST">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="employee_id" value="{{ old('employee_id') ?? $employee->id }}" />
                    <div class="form-scroll">
                        <div id="clone_edu_field">
                            @php
                                $index = 1;
                            @endphp
                            @if (count(old('edu_name', [''])) > 0)
                                @for ($i = 0; $i < count(old('edu_name', [''])); $i++)
                                    @if (count(old('edu_name', [''])) < 2)
                                        @foreach ($employee->educations as $education)
                                            <div class="card">
                                                <div class="card-body">
                                                    <h3 class="card-title">Education Information <a
                                                            href="javascript:void(0);" onclick="deleteClone(event);"
                                                            class="delete-icon"><i class="fa fa-trash-o"></i></a></h3>
                                                    <input type="hidden" name="edu_id[]"
                                                        value="{{ old('edu_id.' . $loop->index) ?? $education->id }}">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Institution</label>
                                                                <input type="text" class="form-control"
                                                                    name="edu_name[]"
                                                                    value="{{ old('edu_name.' . $loop->index) ?? $education->name }}">
                                                                <x-field-validation :errorname="'edu_name.' . $loop->index" />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Subject</label>
                                                                <select class="form-control" name="edu_subject[]">
                                                                    <option value="">--Select--
                                                                    </option>
                                                                    @foreach (\App\Helpers\ProfileHelper::get_education_subject() as $key => $subject)
                                                                        <option value="{{ $key }}"
                                                                            @if ($key == (old('edu_subject.' . $loop->index) ?? $education->subject)) selected @endif>
                                                                            {{ $subject }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <x-field-validation :errorname="'edu_subject.' . $loop->index" />
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Degree</label>
                                                                <input type="text" class="form-control"
                                                                    name="edu_degree[]"
                                                                    value="{{ old('edu_degree.' . $loop->index) ?? $education->degree }}">
                                                                <x-field-validation :errorname="'edu_degree.' . $loop->index" />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Grade</label>
                                                                <input type="text" class="form-control"
                                                                    name="edu_grade[]"
                                                                    value="{{ old('edu_grade.' . $loop->index) ?? $education->grade }}">
                                                                <x-field-validation :errorname="'edu_grade.' . $loop->index" />
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Starting Date</label>
                                                                <div class="cal-icon">
                                                                    <input type="text"
                                                                        class="form-control datetimepicker"
                                                                        name="edu_start_date[]"
                                                                        value="{{ old('edu_start_date.' . $loop->index) ?? $education->start_date }}">
                                                                    <x-field-validation :errorname="'edu_start_date.' . $loop->index" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Complete Date</label>
                                                                <div class="cal-icon">
                                                                    <input type="text"
                                                                        class="form-control datetimepicker"
                                                                        name="edu_complete_date[]"
                                                                        value="{{ old('edu_complete_date.' . $loop->index) ?? $education->complete_date }}">
                                                                    <x-field-validation :errorname="'edu_complete_date.' . $loop->index" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="card" id="edu_{{ $i + 1 }}">
                                        <div class="card-body">
                                            <h3 class="card-title">Education Information <a href="javascript:void(0);"
                                                    onclick="deleteClone(event);" class="delete-icon"><i
                                                        class="fa fa-trash-o"></i></a></h3>
                                            <input type="hidden" name="edu_id[]" value="{{ old('edu_id.' . $i) }}">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Institution</label>
                                                        <input type="text" class="form-control" name="edu_name[]"
                                                            value="{{ old('edu_name.' . $i) }}">
                                                        <x-field-validation :errorname="'edu_name.' . $i" />
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Subject</label>
                                                        <select class="form-control" name="edu_subject[]">
                                                            <option value="">--Select--
                                                            </option>
                                                            @foreach (\App\Helpers\ProfileHelper::get_education_subject() as $key => $subject)
                                                                <option value="{{ $key }}"
                                                                    @if ($key == (old('edu_subject.' . $i))) selected @endif>
                                                                    {{ $subject }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <x-field-validation :errorname="'edu_subject.' . $i" />
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Degree</label>
                                                        <input type="text" class="form-control"
                                                            name="edu_degree[]"
                                                            value="{{ old('edu_degree.' . $i) }}">
                                                        <x-field-validation :errorname="'edu_degree.' . $i" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Grade</label>
                                                        <input type="text" class="form-control" name="edu_grade[]"
                                                            value="{{ old('edu_grade.' . $i) }}">
                                                        <x-field-validation :errorname="'edu_grade.' . $i" />
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Starting Date</label>
                                                        <div class="cal-icon">
                                                            <input type="text" class="form-control datetimepicker"
                                                                name="edu_start_date[]"
                                                                value="{{ old('edu_start_date.' . $i) }}">
                                                        </div>
                                                        <x-field-validation :errorname="'edu_start_date.' . $i" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Complete Date</label>
                                                        <div class="cal-icon">
                                                            <input type="text" class="form-control datetimepicker"
                                                                name="edu_complete_date[]"
                                                                value="{{ old('edu_complete_date.' . $i) }}">
                                                        </div>
                                                        <x-field-validation :errorname="'edu_complete_date.' . $i" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            @endif




                        </div>
                        <div class="add-more">
                            <a href="javascript:void(0);" onclick="add_education_clone();"><i
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
<!-- /Education Modal -->

@push('modal-script')
    <script src="{{ asset('assets/js/profile/education.js') }}"></script>
@endpush

@php
    $education_error_status = false;
    foreach ($errors->getMessages() as $field => $messages) {
        if (substr($field, 0, 4) == 'edu_') {
            $education_error_status = true;
            break;
        }
    }
@endphp

@if ($education_error_status)
    @push('modal-script')
        <script>
            $(document).ready(function() {
                // Code to run when the page is loaded

                $("#education_info").modal("show");
            });
        </script>
    @endpush
@endif
