<div class="row">

    <div class="col-md-3 col-sm-6 col-lg-4 col-xl-2">
        <div class="card dash-widget">
            <div class="card-body">
                <span class="dash-widget-icon"><i class="fa fa-sitemap"></i></span>
                <div class="dash-widget-info">
                    <h3>{{ count($department) }}</h3>
                    <span>Departments</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 col-lg-4 col-xl-2">
        <div class="card dash-widget">
            <div class="card-body">
                <span class="dash-widget-icon"><i class="fa fa-user-circle-o"></i></span>
                <div class="dash-widget-info">
                    <h3>{{ count($designations) }}</h3>
                    <span>Designations</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 col-lg-4 col-xl-2">
        <div class="card dash-widget">
            <div class="card-body">
                <span class="dash-widget-icon"><i class="fa fa-users"></i></span>
                <div class="dash-widget-info">
                    <h3>{{ count($employee) }}</h3>
                    <span>Employees</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 col-lg-4 col-xl-2">
        <div class="card dash-widget">
            <div class="card-body">
                <span class="dash-widget-icon"><i class="fa fa-user"></i></span>
                <div class="dash-widget-info">
                    <h3>{{ $present }}</h3>
                    <span>Present</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 col-lg-4 col-xl-2">
        <div class="card dash-widget">
            <div class="card-body">
                <span class="dash-widget-icon"><i class="fa fa-user-times"></i></span>
                <div class="dash-widget-info">
                    <h3>{{ $absent }}</h3>
                    <span>Absent</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 col-lg-4 col-xl-2">
        <div class="card dash-widget">
            <div class="card-body">
                <span class="dash-widget-icon"><i class="fa fa-user-o"></i></span>
                <div class="dash-widget-info">
                    <h3>{{ $onleave }}</h3>
                    <span>Leave</span>
                </div>
            </div>
        </div>
    </div>
</div>
