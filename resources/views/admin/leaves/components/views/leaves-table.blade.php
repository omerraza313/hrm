<div class="table-responsive">
    <table class="datatable table table-stripped mb-0">
        <thead>
            <tr>
                <th>Id</th>
                <th>Title</th>
                <th>Leave Calendar Upto</th>
                <th>No of Leaves</th>
                <th>Unit</th>
                <th>Carry Forward</th>
                <th>Consecutive</th>
                <th>Leave Type</th>
                <th>Gender Type</th>
                <th>Min.service (days)</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @if (isset($leave_plans))
                @foreach ($leave_plans as $leave_plan)
                    <tr>
                        <td>{{ $leave_plan->id }}</td>
                        <td>{{ $leave_plan->title }}</td>
                        <td>{{ ucfirst(\App\Helpers\LeaveHelper::get_month_name($leave_plan->c_from_date)) .
                            ' - ' .
                            ucfirst(\App\Helpers\LeaveHelper::get_month_name($leave_plan->c_to_date)) }}
                        </td>
                        <td>{{ $leave_plan->quantity }}</td>
                        <td>{{ ucfirst($leave_plan->unit->name) }}</td>
                        <td>{{ $leave_plan->carry_forward . ' Years' }}</td>
                        <td>{{ $leave_plan->consective_leaves . ' Days' }}</td>
                        <td>{{ ucfirst($leave_plan->leave_type->leave_type) }}</td>
                        @php
                            $year = $leave_plan->apply_after_year;
                            $month = $leave_plan->apply_after_month;

                            $currentDate = \Carbon\Carbon::now();

                            // Add the specified number of years and months
                            $currentDate->addYears($year);
                            $currentDate->addMonths($month);

                            // Calculate the difference in days
                            $totalDays = \Carbon\Carbon::now()->diffInDays($currentDate);
                        @endphp
                        <td>{{ ucfirst($leave_plan->leave_gender_type) }}</td>
                        <td>{{ $totalDays }}</td>
                        <td>
                        <button class="btn btn-primary btn-sm text-white"
                        onclick="openEditModal('{{ route('admin.leave.plan.edit', $leave_plan->id) }}')">Edit</button>

                            <button class="btn btn-danger btn-sm text-white"
                            onclick="openDeleteModal('{{ route('admin.leave.plan.delete', $leave_plan->id) }}')">Delete</button>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

@push('modal-script')
    <script>
         function openEditModal(route) {
            let modalId = "#edit_leave_plan_modal";
            $(modalId).modal("show");
            let form = document.getElementById("editForm");
            form.setAttribute("action", route);
        } 
        
        function openDeleteModal(route) {
            let modalId = "#delete_leave_plan_modal";
            $(modalId).modal("show");
            let form = document.getElementById("deleteForm");
            form.setAttribute("action", route);
        }
    </script>
@endpush
