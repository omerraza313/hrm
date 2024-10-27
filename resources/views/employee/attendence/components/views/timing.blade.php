<div class="col-md-4">
    <div class="card pt-2 {{ !$policy ? 'py-5' : '' }}">
        <div class="card-body" style="padding: 0.2rem 1rem;">
            <div class="row">
                <div class="col-lg-12">
                    <h5>ASSIGNED TIMINGS</h5>
                </div>
                <div class="col-lg-12">
                    <h6>Working Days: {{ count($policy->working_day ?? []) }}</h6>
                </div>
                <div class="col-lg-12">
                    @if ($policy)
                        @foreach ($policy->working_day as $day)
                            <span
                                class="bg-primary text-white rounded p-2">{{ \App\Helpers\PolicyHelper::get_policy_days_name($day->day)[0] }}</span>
                        @endforeach
                    @endif
                </div>
                <div class="col-lg-12 mt-2">
                    <h6>
                        @if ($policy)
                            {{ strtoupper(\App\Helpers\PolicyHelper::get_gen_type_name($policy->pay_roll_settings->generation_type)) }}
                        @endif
                    </h6>
                </div>
                <div class="col-lg-6">
                    <div class="text-white bg-primary ps-1">
                        <h6>{{ $policy->working_settings->shift_start ?? '' }}</h6>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-white bg-primary text-end pe-1">
                        <h6>{{ $policy->working_settings->shift_close ?? '' }}</h6>
                    </div>
                </div>
                <div class="col-lg-12">
                    @php
                        $timeDifferenceInHours = 0;
                        if ($policy) {
                            $carbonTime1 = \Carbon\Carbon::createFromFormat('h:i A', $policy->working_settings->shift_start);
                            $carbonTime2 = \Carbon\Carbon::createFromFormat('h:i A', $policy->working_settings->shift_close);

                            $timeDifferenceInHours = $carbonTime1->diffInHours($carbonTime2);
                        }
                    @endphp
                    <h6>Duration: {{ $timeDifferenceInHours }} hrs</h6>
                </div>
            </div>
        </div>
    </div>
</div>
