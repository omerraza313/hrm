<div class="table-responsive">
    <table class="table table-striped custom-table mb-0 datatable">
        <thead>
            <tr>
                <th style="width: 30px;">ID</th>
                <th>Policy Name</th>
                <th>Timings</th>
                <th>Payroll Generation Type</th>
                <th>Overtime</th>
                <th>Timeout Policy</th>
                <th class="text-end">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($policy_list as $k => $policy)
                <tr>
                    <td>{{ $k+1 }}</td>
                    <td>{{ $policy->policy }}</td>
                    <td>{{ $policy->working_settings->shift_start }} - {{ $policy->working_settings->shift_close }}
                    </td>
                    <td>{{ \App\Helpers\PolicyHelper::get_gen_type_name($policy->pay_roll_settings->generation_type) }}
                    </td>
                    <td>{{ \App\Helpers\PolicyHelper::get_ot_status_name($policy->overtime->ot_status) }}</td>

                    <td>{{ \App\Helpers\PolicyHelper::get_timeout_policy_name($policy->working_settings->timeout_policy) }}
                    </td>
                    <td class="text-end">
                        <div class="dropdown dropdown-action">
                            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false"><i class="material-icons">more_vert</i></a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="#"
                                    onclick="openViewModal({{ json_encode($policy) }})"><i class="fa fa-eye m-r-5"></i>
                                    View</a>
                                <a class="dropdown-item" href="#"
                                    onclick="openAssignModal({{ json_encode($policy) }})"><i
                                        class="fa fa-pencil m-r-5"></i>
                                    Assign</a>
                                <a class="dropdown-item" href="#"
                                    onclick="openDeleteModal('{{ route('admin.policy.delete', $policy->id) }}');"><i
                                        class="fa fa-trash-o m-r-5"></i> Delete</a>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@include('admin.policy.components.modals.deletemodal')
@include('admin.policy.components.modals.viewmodal')
@include('admin.policy.components.modals.assignmodal')

@push('modal-script')
    <script src="{{ asset('assets/js/policy/main.js') }}"></script>
    <script>
        function openDeleteModal(route) {
            let modalId = "#delete_policy";
            $(modalId).modal("show");
            let form = document.getElementById("deleteForm");
            form.setAttribute("action", route);
        }

        function openViewModal(policy) {
            console.clear();
            console.log(policy);

            let policyModalView = "#vew_policy_data";

            let policyMap = policy_mapping_html(policy);
            let payrollSetting = policy_payroll_setting_html(policy);
            let workingHours = policy_working_hours_html(policy);
            let workingDays = working_days_html(policy);
            let overTime = policy_overTime_html(policy);
            let holidayOverTime = policy_holiday_overTime_html(policy);
            $(policyModalView).html(`<div class="row">
                ${policyMap}
                ${payrollSetting}
                ${workingHours}
                ${workingDays}
                ${overTime}
                ${holidayOverTime}
                </div>`);
            let modalId = "#view_policy";
            $(modalId).modal("show");
        }
    </script>
@endpush
