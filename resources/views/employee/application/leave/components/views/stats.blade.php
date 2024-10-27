<!-- Leave Statistics -->
<div class="row" id="employee_leave_stats">
    @if ($assignLeaves)
        @foreach ($assignLeaves as $assignLeave)
            <div class="col-md-3">
                <div class="stats-info">
                    <h6>{{ $assignLeave->leave_plan->title }} (Remaining)</h6>
                    <h4>{{ $assignLeave->remaining_leave }} / {{ $assignLeave->leave_plan->quantity }}
                        <span style="font-size: 15px;">{{ $assignLeave->leave_plan->unit->name }}</span>
                    </h4>
                </div>
            </div>
        @endforeach
    @endif
</div>
<!-- /Leave Statistics -->
