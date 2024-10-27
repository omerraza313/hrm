<!-- Add Policy Modal -->
<div id="swap_policy" class="modal custom-modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Swap Policy</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.policy.swap.job.store') }}">
                    @method('POST')
                    @csrf
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="col-form-label">Choose Current Policy</label>
                                <select class="select" name="swap_current_policy">
                                    <option value="">--Select--</option>

                                    @if (isset($policy_list))
                                        @foreach ($policy_list as $policy)
                                            <option value="{{ $policy->id }}" @if (old('swap_current_policy') == $policy->id) selected @endif>
                                                {{ $policy->policy }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>

                                <x-field-validation errorname="swap_current_policy" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="col-form-label">Choose Policy to Swap with</label>
                                <select class="select" name="swap_with_policy">
                                    <option value="">--Select--</option>

                                    @if (isset($policy_list))
                                        @foreach ($policy_list as $policy)
                                            <option value="{{ $policy->id }}" @if (old('swap_with_policy') == $policy->id) selected @endif>
                                                {{ $policy->policy }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>

                                <x-field-validation errorname="swap_with_policy" />
                            </div>
                        </div>


                        <div class="col-lg-12 mt-3">
                            <h4>Swap Scheduling (Optional)</h4>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="col-form-label">Effective From </label>
                                <div class="cal-icon"><input class="form-control datetimepicker" type="text"
                                        name="swap_effect_date" value="{{ old('swap_effect_date') }}">
                                    <x-field-validation errorname="swap_effect_date" />
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="col-form-label">Time </label>
                                <div class="cal-icon"><input class="form-control timepicker" type="text"
                                        name="swap_effect_time" value="{{ old('swap_effect_time') }}">
                                    <x-field-validation errorname="swap_effect_time" />
                                </div>
                            </div>
                            {{-- <div class="form-group">
                                <label>Time <span class="text-danger">*</span></label>
                                <input class="form-control timepicker" type="text" placeholder="--:-- --" name="swap_effect_time"
                                    id="swap_effect_time" value="{{ old('swap_effect_time') }}">
                                <x-field-validation errorname="swap_effect_time" />
                            </div> --}}
                        </div>


                        <div class="col-lg-12 mt-3">
                            <h4>Rollback (Optional)</h4>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="col-form-label">Rollback Date </label>
                                <div class="cal-icon"><input class="form-control datetimepicker" type="text"
                                        name="swap_rollback_date" value="{{ old('swap_rollback_date') }}">
                                    <x-field-validation errorname="swap_rollback_date" />
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="col-form-label">Time </label>
                                <div class="cal-icon"><input class="form-control timepicker" type="text"
                                        name="swap_rollback_time" value="{{ old('swap_rollback_time') }}">
                                    <x-field-validation errorname="swap_rollback_time" />
                                </div>
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
<!-- /Add Policy Modal -->

@push('modal-script')

@endpush
