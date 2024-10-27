<!-- Leave Statistics -->
<div class="row">
    <div class="col-md-3">
        <div class="card dash-widget">
            <div class="card-body">
                <span class="dash-widget-icon">
                    <i class="fa fa-tasks"></i>
                </span>
                <div class="dash-widget-info">
                    <h3>{{ count($total) }}</h3>
                    <span>Total Applications</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dash-widget">
            <div class="card-body">
                <span class="dash-widget-icon">
                    <i class="fa fa-ban"></i>
                </span>
                <div class="dash-widget-info">
                    <h3>{{ count($declined) }}</h3>
                    <span>Declined Applications</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dash-widget">
            <div class="card-body">
                <span class="dash-widget-icon">
                    <i class="fa fa-clock-o"></i>
                </span>
                <div class="dash-widget-info">
                    <h3>{{ count($pendings) }}</h3>
                    <span>Pending Applications</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dash-widget">
            <div class="card-body">
                <span class="dash-widget-icon">
                    <i class="fa fa-check"></i>
                </span>
                <div class="dash-widget-info">
                    <h3>{{ count($approved) }}</h3>
                    <span>Approved Applications</span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Leave Statistics -->
