<div class="col-md-4">
    <div class="card bg-primary pt-1">
        <div class="card-body text-white">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h5>Earned / Total</h5>
                </div>
                <div class="col-lg-6">
                    <h5>ME</h5>
                    <h5>{{ $workingHours }} Hrs / {{ $totalWorkingHours }} Hrs</h5>
                </div>
                <div class="col-lg-12 border mb-3 mt-1">

                </div>
                <div class="col-lg-6">
                    <h5>Present / Total<h5>
                </div>
                <div class="col-lg-6">
                    <h5>ME</h5>
                    <h5>{{ count($presentAttendence) }} / {{ $totalWorkingDays }} Days</h5>
                </div>
            </div>
        </div>
    </div>
</div>
