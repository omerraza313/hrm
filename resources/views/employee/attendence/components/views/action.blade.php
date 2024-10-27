@php
    $lastDate = \Carbon\Carbon::parse($lastAttendence?->leave_time);
    $lastDate = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $lastDate, 'UTC');
    $lastDate = $lastDate->setTimezone('America/New_York');

    $Day = $lastDate->format('l');
    $d = $lastDate->format('d');
    $m = $lastDate->format('m');
    $year = $lastDate->format('Y');
    $time = $lastDate->format('h:i A');

//    $date = $lastDate->format('jS');
//    $month = $lastDate->format('F');
@endphp
<div class="col-md-4">
    <div class="card pt-2 pb-2">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12">
                    <h5>ACTIONS</h5>
                </div>
                <div class="col-lg-12 text-center">
                    <h6>{{ $Day }} {{ $m }}/{{ $d }}/{{ $year }}</h6>
                </div>
                <div class="col-lg-12">
                    <div class="row align-items-center">
                        <div class="col-lg-5 text-end">
                            <h4>Checkout at </h4>
                        </div>
                        <div class="col-lg-7">
                            <span style="font-weight: 300; font-size: 15px;">{{ $time }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 text-center">
                    <div class="row">
                        @if (!$todayAttendence?->arrival_time)
                            <div class="col-lg-12 text-center">
                                <form action="{{ route('employee.attendence.mark.arrival') }}" method="POST">
                                    @csrf
                                    @method('POST')
                                    <input type="submit" class="btn btn-sm btn-primary" value="Check In">
                                </form>
                            </div>
                        @endif

                        @if ($todayAttendence && ($todayAttendence?->arrival_time || !$todayAttendence?->leave_time))
                            <div class="col-lg-12 text-center">
                                <form
                                    action="{{ route('employee.attendence.mark.leave', ['id' => optional($todayAttendence)->id]) }}                                    "
                                    method="POST">
                                    @csrf
                                    @method('POST')
                                    <input type="submit" class="btn btn-sm btn-primary" value="Check Out">
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
