<div class="col-lg-12">
    <h4>Overtime and Leave Settings</h4>
</div>
<div class="col-lg-12">
    <div class="form-group">
        <label>Daily Overtime Counter <span class="text-danger">*</span></label>
        <select class="form-control" name="add_policy_overtime_status" id="add_policy_overtime_status"
            onchange="over_time();">
            @foreach (\App\Helpers\PolicyHelper::get_over_time_status() as $key => $overtime_status)
                <option value="{{ $key }}" @if ($key == old('add_policy_overtime_status')) selected @endif>
                    {{ $overtime_status }}</option>
            @endforeach
        </select>
        <x-field-validation errorname="add_policy_overtime_status" />
    </div>
</div>
<div class="col-lg-12" id="over_time_fields"
    style="display: {{ old('add_policy_overtime_status') == 2 ? 'block' : 'none' }};">
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                <label>Overtime starts after following min of duty closing <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="add_policy_ot_atfer_closing_duty"
                    value="{{ old('add_policy_ot_atfer_closing_duty') ?? 0 }}">
                <x-field-validation errorname="add_policy_ot_atfer_closing_duty" />
            </div>
        </div>

        <div class="col-lg-12">
            <div class="form-group">
                <label>Minimum minute(s) required for overtime <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="add_policy_ot_min_minutes"
                    value="{{ old('add_policy_ot_min_minutes') ?? 0 }}">
                <x-field-validation errorname="add_policy_ot_min_minutes" />
            </div>
        </div>

        <div class="col-lg-12">
            <div class="form-group">
                <label>Overtime Rate <span class="text-danger">*</span></label>
                <select class="form-control" name="add_policy_ot_rate" id="add_policy_ot_rate"
                    onchange="overtimeRate();">
                    <option value="">Select Policy Rate</option>
                    @foreach (\App\Helpers\PolicyHelper::get_over_time_rate() as $key => $overtime_status)
                        <option value="{{ $key }}" @if ($key == old('add_policy_ot_rate')) selected @endif>
                            {{ $overtime_status }}</option>
                    @endforeach
                </select>
                <x-field-validation errorname="add_policy_ot_rate" />
            </div>
        </div>


        <div class="col-lg-12" id="ot_rate_fields"
            style="display: {{ old('add_policy_ot_rate') == 1 || old('add_policy_ot_rate') == 3 ? 'block' : 'none' }};">
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>Overtime Rate <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="add_policy_ot_rate_value"
                            value="{{ old('add_policy_ot_rate_value') ?? 0 }}">
                        <x-field-validation errorname="add_policy_ot_rate_value" />
                    </div>
                </div>
                <div class="col-lg-12" id="fixed_rate_ot"
                    style="display: {{ old('add_policy_ot_rate') == 1 ? 'block' : 'none' }};">
                    <div class="form-group">
                        <label>Overtime Amount <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" name="add_policy_ot_amount"
                            value="{{ old('add_policy_ot_amount') ?? 0 }}">
                        <x-field-validation errorname="add_policy_ot_amount" />
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>

<div class="col-lg-12">
    <div class="form-group">
        <label>Holiday/Weekend Overtime <span class="text-danger">*</span></label>
        <select class="form-control" name="add_policy_holiday_ot" id="add_policy_holiday_ot"
            onchange="holidayOTRate();">
            {{-- <option value="">Select Policy Rate</option> --}}
            @foreach (\App\Helpers\PolicyHelper::get_holiday_over_time_rate() as $key => $overtime_status)
                <option value="{{ $key }}" @if ($key == old('add_policy_holiday_ot')) selected @endif>
                    {{ $overtime_status }}</option>
            @endforeach
        </select>
        <x-field-validation errorname="add_policy_holiday_ot" />
    </div>
</div>

<div class="col-lg-12" id="holiday_ot_rate_fields"
    style="display: {{ old('add_policy_holiday_ot') == 3 || old('add_policy_holiday_ot') == 4 || old('add_policy_holiday_ot') == 5 ? 'block' : 'none' }};">
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                <label>Overtime Rate <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="add_policy_holiday_ot_rate"
                    value="{{ old('add_policy_holiday_ot_rate') ?? 0 }}">
                <x-field-validation errorname="add_policy_holiday_ot_rate" />
            </div>
        </div>
        <div class="col-lg-12" id="fixed_holiday_rate_ot"
            style="display: {{ old('add_policy_holiday_ot') == 3 || old('add_policy_holiday_ot') == 4 ? 'block' : 'none' }};">
            <div class="form-group">
                <label>Overtime Amount <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="add_policy_holiday_ot_amount"
                    value="{{ old('add_policy_holiday_ot_amount') ?? 0 }}">
                <x-field-validation errorname="add_policy_holiday_ot_amount" />
            </div>
        </div>
    </div>
</div>



@push('modal-script')
    {{-- over_time_fields --}}
    <script>
        function over_time() {
            let otStatus = document.getElementById('add_policy_overtime_status');
            if (otStatus.value == '1') {
                $("#over_time_fields").css({
                    'display': 'none'
                });
            } else {
                $("#over_time_fields").css({
                    'display': 'block'
                });
            }
        }

        function overtimeRate() {
            let otStatus = document.getElementById('add_policy_ot_rate').value;
            if (otStatus == '1') {
                $("#ot_rate_fields").css({
                    'display': 'block'
                });
                $("#fixed_rate_ot").css({
                    'display': 'block'
                });
            } else if (otStatus == '3') {
                $("#ot_rate_fields").css({
                    'display': 'block'
                });
                $("#fixed_rate_ot").css({
                    'display': 'none'
                });
            } else {
                $("#ot_rate_fields").css({
                    'display': 'none'
                });
                $("#fixed_rate_ot").css({
                    'display': 'none'
                });
            }
        }

        function holidayOTRate() {
            let otStatus = document.getElementById('add_policy_holiday_ot').value;
            if (otStatus == "3") {
                $("#holiday_ot_rate_fields").css({
                    'display': 'block'
                });
                $("#fixed_holiday_rate_ot").css({
                    'display': 'block'
                });
            } else if (otStatus == "4") {
                $("#holiday_ot_rate_fields").css({
                    'display': 'block'
                });
                $("#fixed_holiday_rate_ot").css({
                    'display': 'block'
                });
            } else if (otStatus == "5") {
                $("#holiday_ot_rate_fields").css({
                    'display': 'block'
                });
                $("#fixed_holiday_rate_ot").css({
                    'display': 'none'
                });
            } else {
                $("#holiday_ot_rate_fields").css({
                    'display': 'none'
                });
                $("#fixed_holiday_rate_ot").css({
                    'display': 'none'
                });
            }
        }
    </script>
@endpush
