<div class="col-lg-12 mt-3">
    <h4>Payroll setting</h4>
</div>
<div class="col-lg-12">
    <div class="form-group">
        <label class="col-form-label">Payslip Generation Type</label>
        <select class="select pay_gen_type" id="pay_gen_type" name="add_policy_payslip_gen_type"
            onchange="payslipgenerationtypechange();">
            <option value="">Payslip Generation Type</option>
            @foreach (\App\Helpers\PolicyHelper::pay_gen_type() as $key => $gen_type)
                <option value="{{ $key }}" @if ($key == old('add_policy_payslip_gen_type')) selected @endif>{{ $gen_type }}
                </option>
            @endforeach
        </select>
        <x-field-validation errorname="add_policy_payslip_gen_type" />
    </div>
</div>
<!--
<div class="col-lg-12">
    <div class="d-flex align-items-center">
        {{-- <div class="form-group"> --}}
        <label class="col-form-label me-2">From</label>
        <select class="form-control" style="width: 15%" name="add_policy_from">
            <option>1st</option>
            <option>Time Base</option>
            <option>Attendance Base</option>
            <option>Hourly Base</option>
        </select>
        {{-- </div> --}}
        {{-- <div class="form-group"> --}}
        <label class="col-form-label mx-2">Of</label>
        <select class="form-control" style="width: 40%" name="add_policy_month">
            <option>Current Month</option>
            <option>Prev Month</option>
        </select>
        {{-- </div> --}}
        {{-- <div class="form-group"> --}}
        <label class="col-form-label mx-2">To</label>
        <select class="form-control" style="width: 15%" name="add_policy_to">
            <option>1st</option>
            <option>Time Base</option>
            <option>Attendance Base</option>
            <option>Hourly Base</option>
        </select>
        <label class="col-form-label mx-2">of next month</label>
        {{-- </div> --}}
    </div>
</div>
-->
<div class="col-lg-12 mt-3">
    <div class="form-group">
        <label>Off Days Allowed Per Month <span class="text-danger">*</span></label>
        <input class="form-control" type="number" value="{{ old('add_policy_off_days') ?? 0 }}"
            name="add_policy_off_days">
        <x-field-validation errorname="add_policy_off_days" />
    </div>
</div>

{{-- Hourly Base Fields --}}
<div class="col-lg-12" id="hourly_base_fields"
    style="display: {{ old('add_policy_payslip_gen_type') == '3' ? 'block' : 'none' }};">
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                <label>Required Working Hours <span class="text-danger">*</span></label>
                <input class="form-control" type="number" placeholder="Number of daily working hours"
                    value="{{ old('add_policy_working_hours') }}" name="add_policy_working_hours">
                <x-field-validation errorname="add_policy_working_hours" />
            </div>
        </div>
        {{-- <div class="col-lg-12">
            <div class="form-group">
                <label>Required Minutes <span class="text-danger">*</span></label>
                <input class="form-control" type="number" placeholder="Minutes" name="add_policy_minutes"
                    value="{{ old('add_policy_minutes') }}">
                <x-field-validation errorname="add_policy_minutes" />
            </div>
        </div> --}}
        <div class="col-lg-12">
            <div class="form-group">
                <label>Max Shift Retaining Hours <span class="text-danger">*</span></label>
                <input class="form-control" type="number"
                    placeholder="Numbers of hours system waits before starting new attendance session"
                    name="add_policy_shift_hours" value="{{ old('add_policy_shift_hours') }}">
                <x-field-validation errorname="add_policy_shift_hours" />
            </div>
        </div>
    </div>
</div>
