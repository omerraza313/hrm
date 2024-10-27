<!-- Leave Statistics -->
<div class="row">
    <div class="col-md-4">
        <div class="stats-info">
            <h6>Total Applications</h6>
            <h4>{{ count($total) }}</h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-info">
            <h6>Pending Applications</h6>
            <h4 id="leave_count_1">{{ count($pendings) }}</h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-info">
            <h6>Approved Applications</h6>
            <h4>{{ count($approved) }}</h4>
        </div>
    </div>
</div>
<!-- /Leave Statistics -->
