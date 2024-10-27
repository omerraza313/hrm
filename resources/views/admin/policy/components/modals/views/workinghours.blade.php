<div class="col-lg-12">
    <h4>Working Hours</h4>
</div>
<div class="col-lg-4">
    <div class="form-group">
        <label>Shift Start Time <span class="text-danger">*</span></label>
        <input class="form-control timepicker" type="text" placeholder="--:-- --" name="add_policy_shift_start"
            id="add_policy_shift_start" value="{{ old('add_policy_shift_start') }}">
        <x-field-validation errorname="add_policy_shift_start" />
    </div>
</div>
<div class="col-lg-4">
    <div class="form-group">
        <label>Late Coming Leniency Time <span class="text-danger">*</span></label>
        <input class="form-control" type="number" placeholder="In minutes" name="add_policy_late_c_l_t"
            value="{{ old('add_policy_late_c_l_t') }}">
        <x-field-validation errorname="add_policy_late_c_l_t" />
    </div>
</div>
<div class="col-lg-4">
    <div class="form-group">
        <label>Early Arrival Policy <span class="text-danger">*</span></label>
        <select class="form-control" name="add_policy_e_a_p">
            <option value="">Select Arrival Policy</option>
            @foreach (\App\Helpers\PolicyHelper::early_arrival_policy() as $key => $early_arrival_policy)
                <option value="{{ $key }}" @if ($key == old('add_policy_e_a_p')) selected @endif>
                    {{ $early_arrival_policy }}</option>
            @endforeach
        </select>
        <x-field-validation errorname="add_policy_e_a_p" />
    </div>
</div>


<div class="col-lg-4">
    <div class="form-group">
        <label>Shift Closing Time <span class="text-danger">*</span></label>
        <input class="form-control timepicker" type="text" placeholder="--:-- --" name="add_policy_shift_close"
            id="add_policy_shift_close" value="{{ old('add_policy_shift_close') }}">
        <x-field-validation errorname="add_policy_shift_close" />
    </div>
</div>


<div class="col-lg-4">
    <div class="form-group">
        <label>Force Timeout <span class="text-danger">*</span></label>
        <select class="form-control" name="add_policy_force_timeout">
            <option value="">Select Force Timeout</option>
            @foreach (\App\Helpers\PolicyHelper::get_force_timeout() as $key => $time_out)
                <option value="{{ $key }}" @if ($key == old('add_policy_force_timeout')) selected @endif>
                    {{ $time_out }}</option>
            @endforeach
        </select>
        <x-field-validation errorname="add_policy_force_timeout" />
    </div>
</div>
<div class="col-lg-4">
    <div class="form-group">
        <label>Timeout Policy <span class="text-danger">*</span></label>
        <select class="form-control" name="add_policy_timeout_policy">
            <option value="">Select Timeout Policy</option>
            @foreach (\App\Helpers\PolicyHelper::get_timeout_policy() as $key => $timeout_policy)
                <option value="{{ $key }}" @if ($key == old('add_policy_timeout_policy')) selected @endif>
                    {{ $timeout_policy }}</option>
            @endforeach
        </select>
        <x-field-validation errorname="add_policy_timeout_policy" />
    </div>
</div>



<div class="col-lg-4">
    <div class="form-group">
        <label>Late Minute Monthly Bucket <span class="text-danger">*</span></label>
        <input class="form-control" type="number" placeholder="Max value 999" name="add_policy_monthly_late_minute"
            value="{{ old('add_policy_monthly_late_minute') ?? 0 }}">
        <x-field-validation errorname="add_policy_monthly_late_minute" />
    </div>
</div>

<div class="col-lg-4">
    <div class="form-group">
        <label>Late Comers Penalty <span class="text-danger">*</span></label>
        <input class="form-control" type="number" placeholder="Max value 4" name="add_policy_late_comers_penalty"
            value="{{ old('add_policy_late_comers_penalty') ?? 0 }}">
        <x-field-validation errorname="add_policy_late_comers_penalty" />
    </div>
</div>
