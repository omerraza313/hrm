<input type="hidden" id="total_attendances" value="{{ json_encode($attendances) }}">
<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12 text-center">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">{{ \Carbon\Carbon::now()->format('M Y') }} Attendance</h3>
                        <div id="bar-charts"></div>
                    </div>
                </div>
            </div>
            {{-- <div class="col-md-6 text-center">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Sales Overview</h3>
                        <div id="line-charts"></div>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
</div>
