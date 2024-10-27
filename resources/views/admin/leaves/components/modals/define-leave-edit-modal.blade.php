<!-- Add Edit Leave Category Modal -->
<div  class="modal custom-modal fade" role="dialog" id="edit_leave_plan_modal">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Define Leave Type123</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form action="{{ isset($leavePlan) ? route('admin.leave.plan.edit', $leavePlan->id) : route('admin.leave.plan') }}" method="post" enctype="multipart/form-data" id="editForm">
            @csrf
                    @if(isset($leavePlan))
                        @method('PUT')
                    @else
                        @method('POST')
                    @endif
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Leave Title <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="leave_plan_title"
                                placeholder="Please Enter Leave Title" value="{{ old('leave_plan_title', $leavePlan->title ?? '') }}" />
                                <x-field-validation errorname="leave_plan_title" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Calendar from <span class="text-danger">*</span></label>
                                <select class="select" name="leave_plan_from">
                                    <option value="">Select start month</option>
                                    @if (isset($months) && is_array($months))
                                        @foreach ($months as $month)
                                            <option value="{{ $month }}"
                                                @if (old('leave_plan_from') == $month) selected @endif>{{ ucfirst($month) }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <x-field-validation errorname="leave_plan_from" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Calendar upto <span class="text-danger">*</span></label>
                                <select class="select" name="leave_plan_to">
                                    <option value="">Select upto month</option>
                                    @if (isset($months) && is_array($months))
                                        @foreach ($months as $month)
                                            <option value="{{ $month }}"
                                                @if (old('leave_plan_to') == $month) selected @endif>{{ ucfirst($month) }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <x-field-validation errorname="leave_plan_to" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Number of Days/Hours <span class="text-danger">*</span></label>
                                <input class="form-control" type="number" name="leave_plan_quantity"
                                    placeholder="Please Enter Quantity" value="{{ old('leave_plan_quantity') }}" />
                                <x-field-validation errorname="leave_plan_quantity" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Unit<span class="text-danger">*</span></label>
                                <select class="select" name="leave_plan_unit">
                                    @if (isset($units))
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}"
                                                @if (old('leave_plan_unit') == $unit->id) selected @endif>
                                                {{ ucfirst($unit->name) }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <x-field-validation errorname="leave_plan_unit" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>No Of Years<span class="text-danger">*</span></label>
                                <input class="form-control" type="number" name="leave_plan_carry_f"
                                    placeholder="Please Enter Quantity" value="{{ old('leave_plan_carry_f') }}" />
                                <x-field-validation errorname="leave_plan_carry_f" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Consecutive Allowed<span class="text-danger">*</span></label>
                                <input class="form-control" type="number" name="leave_plan_con_allow"
                                    placeholder="Please Enter Quantity" value="{{ old('leave_plan_con_allow') }}" />
                                <x-field-validation errorname="leave_plan_con_allow" />
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <label>For New Joiners Applicable After<span class="text-danger">*</span></label>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Year<span class="text-danger">*</span></label>
                                <input class="form-control" type="number" name="leave_plan_apply_year"
                                    placeholder="Please Enter Quantity"
                                    value="{{ old('leave_plan_apply_year') ?? '0' }}" />
                                <x-field-validation errorname="leave_plan_apply_year" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Month<span class="text-danger">*</span></label>
                                <input class="form-control" type="number" name="leave_plan_apply_month"
                                    placeholder="Please Enter Quantity"
                                    value="{{ old('leave_plan_apply_month') ?? '3' }}" />
                                <x-field-validation errorname="leave_plan_apply_month" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Leave Type<span class="text-danger">*</span></label>
                                <select class="select" name="leave_plan_type">
                                    @if (isset($leave_types))
                                        @foreach ($leave_types as $leave_type)
                                            <option value="{{ $leave_type->id }}"
                                                @if (old('leave_plan_type') == $leave_type->id) selected @endif>
                                                {{ ucfirst($leave_type->leave_type) }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <x-field-validation errorname="leave_plan_type" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Gender<span class="text-danger">*</span></label>
                                <select class="select" name="leave_plan_gender_type">
                                    <option value="Male" @if (old('leave_plan_gender_type') == 'Male') selected @endif>
                                        Male
                                    </option>
                                    <option value="Female" @if (old('leave_plan_gender_type') == 'Female') selected @endif>
                                        Female
                                    </option>
                                    <option value="both" @if (old('leave_plan_gender_type') == 'both') selected @endif>
                                        Both
                                    </option>
                                </select>
                                <x-field-validation errorname="leave_plan_gender_type" />
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

@php
    $leave_plan_modal_status = false;
    foreach ($errors->getMessages() as $field => $messages) {
        if (substr($field, 0, 11) == 'leave_plan_') {
            $leave_plan_modal_status = true;
        }
    }
@endphp
@if ($leave_plan_modal_status)
    @push('modal-script')
        <script>
            $(document).ready(function() {
                $("#edit_leave_plan_modal").modal("show");
            });
        </script>
    @endpush
@endif