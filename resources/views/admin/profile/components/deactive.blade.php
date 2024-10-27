<div class="card-body">
    <h3 class="card-title">Deactive Details

    </h3>
    <div class="experience-box">
        <ul class="personal-info deactive_details">
            <li>
                <div class="title">Notice Period Served </div>
                <div class="text">{{ $employee->deactive_user->notice_period_served == '1' ? 'Yes' : 'No' }}</div>
            </li>
            <li>
                <div class="title">Notice Period Date </div>
                <div class="text">
                    {{ \App\Helpers\DateHelper::dateslashformat($employee->deactive_user->notice_period_date) }}</div>
            </li>
            <li>
                <div class="title">Notice Period Duration </div>
                <div class="text">{{ $employee->deactive_user->notice_period_duration }}</div>
            </li>
            <li>
                <div class="title">Exit Date </div>
                <div class="text">{{ \App\Helpers\DateHelper::dateslashformat($employee->deactive_user->exit_date) }}
                </div>
            </li>
            <li>
                <div class="title">All Cleared </div>
                <div class="text">{{ ucfirst($employee->deactive_user->all_cleared) }}</div>
            </li>
            <li>
                <div class="title">Reason </div>
                <div class="text">
                    {{ \App\Helpers\EmployeeHelper::get_reasons_name($employee->deactive_user->reason) }}</div>
            </li>
            <li>
                <div class="title">Comments </div>
                <div class="text">{{ $employee->deactive_user->comments }}</div>
            </li>
        </ul>
    </div>
</div>

@include('admin.profile.components.modals.experiencemodal')
