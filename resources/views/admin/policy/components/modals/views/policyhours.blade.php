<div class="col-lg-4">

</div>
<div class="col-lg-12 my-3">

</div>
<div class="col-lg-4 text-center">
    <h5>Days</h5>
</div>
<div class="col-lg-4 text-center">
    <h5>Start Time</h5>
</div>
<div class="col-lg-4 text-center">
    <h5>Close Time</h5>
</div>

@php
    $index = 0;

    // Get old input or existing policy data
    $oldData = old('add_policy_working_array') 
        ? collect(json_decode(old('add_policy_working_array')))
        : null;

    if(isset($policy)) {
        $arryData = $oldData ?? collect(\App\Helpers\PolicyHelper::get_policy_hours())->map(function ($default) use ($policy) {
            $existing = $policy->working_day->firstWhere('day', $default->id) ?? null; 
            return (object) [
                'id' => $default->id,
                'day' => $default->day,
                'start_time' => $existing->start_time ?? $default->start_time,
                'end_time' => $existing->close_time ?? $default->end_time,
                'active' => $existing->active ?? $default->active,
            ];
        });
    }
    else{
        $arryData = old('add_policy_working_array') ? collect(json_decode(old('add_policy_working_array'))) : \App\Helpers\PolicyHelper::get_policy_hours();
    }

@endphp

<input type="hidden" name="add_policy_working_array" id="add_policy_working_array"
    value="{{ old('add_policy_working_array', json_encode($arryData)) }}">

@foreach ($arryData as $policy_hour)
    <input type="hidden" id="{{ 'single_data_' . $policy_hour->id }}" 
           value="{{ json_encode($policy_hour) }}">
    <div class="col-lg-4">
        <div class="form-group">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="add_policy_working_day_{{ $policy_hour->id }}"
                    name="add_policy_working_day[]" value="{{ $policy_hour->id }}"
                    {{ old('add_policy_working_day.' . $index, $policy_hour->active) ? 'checked' : '' }}
                    onchange="workingHours('{{ json_encode($policy_hour) }}');">
                <label class="form-check-label" for="add_policy_working_day_{{ $policy_hour->id }}">
                    {{ $policy_hour->day }}
                </label>
            </div>
        </div>
        <x-field-validation :errorname="'add_policy_working_day.' . $index" />
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            <input class="form-control timepicker {{ 'single_data_'.$policy_hour->id }}" type="text"
                placeholder="--:-- --" name="add_policy_working_start_shift[]"
                id="{{ 'add_policy_working_start_shift_' . $policy_hour->id }}"
                value="{{ old('add_policy_working_start_shift.' . $index, $policy_hour->start_time) }}">
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            <input class="form-control timepicker {{ 'single_data_'.$policy_hour->id }}" type="text"
                placeholder="--:-- --" name="add_policy_working_end_shift[]"
                id="{{ 'add_policy_working_end_shift_' . $policy_hour->id }}"
                value="{{ old('add_policy_working_end_shift.' . $index, $policy_hour->end_time) }}">
        </div>
    </div>
    @php
        $index++;
    @endphp
@endforeach


<div class="col-lg-12">
    <x-field-validation :errorname="'add_policy_working_day'" />
</div>

@push('modal-script')
    <script>
        function workingHours(data) {
            console.clear();
            let dataArray = JSON.parse(data);
            console.log(dataArray);

            let allData = document.getElementById('add_policy_working_array');
            let allDataArray = JSON.parse(allData.value);
            console.log(allDataArray);

            var indexOfId = getIndexById(dataArray.id, allDataArray);

            let workingDay = document.getElementById('add_policy_working_day_' + dataArray.id);
            if (workingDay.checked) {
                // dataArray.active = 1;
                allDataArray[indexOfId].active = 1;

                let startTime = document.getElementById('add_policy_working_start_shift_' + dataArray.id);
                let endTime = document.getElementById('add_policy_working_end_shift_' + dataArray.id);

                let shiftStartTime = document.getElementById('add_policy_shift_start');
                let shiftEndTime = document.getElementById('add_policy_shift_close');

                console.log(startTime.value);
                if (startTime.value == '') {
                    console.log("working");
                    allDataArray[indexOfId].start_time = shiftStartTime.value;
                } else {
                    allDataArray[indexOfId].start_time = startTime.value;
                }

                if (endTime.value == '') {
                    allDataArray[indexOfId].end_time = shiftEndTime.value;
                } else {
                    allDataArray[indexOfId].end_time = endTime.value;
                }

            } else {
                // dataArray.active = 0;
                allDataArray[indexOfId].active = 0;
                allDataArray[indexOfId].start_time = '';
                allDataArray[indexOfId].end_time = '';
            }



            allData.value = JSON.stringify(allDataArray);
        }


        function getIndexById(searchId, dataArray) {
            return dataArray.findIndex(function(day) {
                return day.id === searchId;
            });
        }

        // Handle the change event
        $('.timepicker').on('dp.change', function(event) {
            // var selectedDate = event.date.format('YYYY-MM-DD HH:mm:ss');
            // console.log('Selected Date and Time:', selectedDate);
            console.clear();
            let data = document.getElementById('add_policy_working_array').value;
            console.log(data);
            let dataArray = JSON.parse(data);
            dataArray.forEach(element => {
                console.log(element);
                workingHours(JSON.stringify(element));
            });
            // console.log(event.target.classList);
            // let data = document.getElementById(event.target.classList[2]).value;
            // workingHours(data);

        });
    </script>
@endpush
