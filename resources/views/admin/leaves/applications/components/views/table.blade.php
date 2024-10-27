<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped custom-table mb-0 leaveadmintable">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Employee</th>
                        <th>Subject</th>
                        <th>From</th>
                        <th>To</th>
                        <th>No of Days/Hours</th>
                        <th>Reason</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($apply_leaves)
                        @foreach ($apply_leaves as $apply_leave)
                            <tr>
                                <td>{{ $apply_leave->id }}</td>
                                <td>
                                    {{-- <h2 class="table-avatar">
                                        <a href="profile.html" class="avatar"><img alt=""
                                                src="{{ asset('assets/img/profiles/avatar-09.jpg') }}"></a>
                                        <a href="#">{{ $apply_leave->employee->first_name }}
                                            {{ $apply_leave->employee->last_name }}
                                            <span>{{ $apply_leave->employee->employee_details->designation->name }}</span></a>
                                    </h2> --}}

                                    <h2 class="table-avatar">
                                        <a href="{{ route('profile.view', $apply_leave->employee->id) }}"
                                            class="avatar"><img alt=""
                                                src="{{ $apply_leave->employee->image ? asset('images/employee/') . '/' . $apply_leave->employee->image : asset('assets/img/profiles/avatar-02.jpg') }}"></a>
                                        <a href="#">{{ $apply_leave->employee->first_name }}
                                            {{ $apply_leave->employee->last_name }}
                                        </a>
                                    </h2>
                                </td>
                                <td>{{ $apply_leave->subject }}</td>
                                <td>{{ \App\Helpers\DateHelper::dateformat('m/d/Y', $apply_leave->leave_from) }}</td>
                                <td>{{ \App\Helpers\DateHelper::dateformat('m/d/Y', $apply_leave->leave_upto) }}</td>
                                {{-- <td>{{ \App\Helpers\DateHelper::calculateNumberOfDays($apply_leave->leave_from, $apply_leave->leave_upto) }}
                                    days</td> --}}
                                <td>
                                    @php
                                        $noOfDays = 0;
                                        $noOfHours = 0;
                                        foreach ($apply_leave->adjust_leaves as $adjust_leave) {
                                            if ($adjust_leave->leave_plan->unit_id == 1) {
                                                $noOfDays += $adjust_leave->quantity;
                                            } else {
                                                $noOfHours += $adjust_leave->quantity;
                                            }
                                        }
                                    @endphp
                                    {{ $noOfDays != 0 ? $noOfDays . ' Days' : '' }}
                                    {{ $noOfHours != 0 ? $noOfHours . ' Hours' : '' }}
                                </td>
                                <td>{{ Str::limit($apply_leave->body, 10) }}....</td>
                                <td class="text-center">
                                    <div class="dropdown action-label">
                                        @if ($apply_leave->status == \App\Enums\ApprovedStatusEnum::Pending->value)
                                            <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#"
                                                data-bs-toggle="dropdown" aria-expanded="false"><i
                                                    class="fa fa-dot-circle-o text-info"></i> Pending</a>
                                        @elseif ($apply_leave->status == \App\Enums\ApprovedStatusEnum::Approved->value)
                                            <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#"
                                                data-bs-toggle="dropdown" aria-expanded="false" data-bs-toggle="modal"
                                                data-bs-target="#approve_leave"><i
                                                    class="fa fa-dot-circle-o text-success"></i>
                                                Approved</a>
                                        @else
                                            <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#"
                                                data-bs-toggle="dropdown" aria-expanded="false"><i
                                                    class="fa fa-dot-circle-o text-danger"></i> Declined</a>
                                        @endif
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="#"
                                                onclick="changeApproveStatus('Approved', '{{ $apply_leave->id }}', '{{ route('admin.leave.application.update.status') }}?apply_leave_id={{ $apply_leave->id }}&status={{ \App\Enums\ApprovedStatusEnum::Approved->value }}');"><i
                                                    class="fa fa-dot-circle-o text-success"></i> Approved</a>
                                            <a class="dropdown-item" href="#"
                                                onclick="changeApproveStatus('Declined', '{{ $apply_leave->id }}', '{{ route('admin.leave.application.update.status') }}?apply_leave_id={{ $apply_leave->id }}&status={{ \App\Enums\ApprovedStatusEnum::Declined->value }}');"><i
                                                    class="fa fa-dot-circle-o text-danger"></i> Declined</a>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                                            aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="#"
                                                onclick="viewLeaveFunction('{{ $apply_leave }}');"><i
                                                    class="fa fa-pencil m-r-5"></i> View</a>
                                            <a class="dropdown-item" href="#"
                                                onclick="deleteLeaveFunction('{{ route('admin.delete.leave.application', $apply_leave->id) }}');"><i
                                                    class="fa fa-trash-o m-r-5"></i>
                                                Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('admin.leaves.applications.components.modals.statusmodal')
@include('admin.leaves.applications.components.modals.deletemodal')
@include('admin.leaves.applications.components.modals.viewleave')

@push('modal-script')
    <script>
        $('.leaveadmintable').DataTable({
            order: [
                [0, 'desc']
            ]
        });


        function deleteLeaveFunction(route) {

            let modalId = "#delete_leave";
            $(modalId).modal("show");
            let form = document.getElementById("deleteForm");
            form.setAttribute("action", route);
            // delete_leave
        }


        function viewLeaveFunction(applyLeave) {
            //
            if (typeof applyLeave == 'string') {
                applyLeave = JSON.parse(applyLeave);
            }

            console.clear();
            // console.log(typeof JSON.parse(applyLeave));
            console.log(applyLeave);

            let modalId = "#view_leave";
            let modalData = document.getElementById("view_modal_data");
            let tableData = tableString(applyLeave);
            modalData.innerHTML = tableData;
            leaveadjust(applyLeave);
            $(modalId).modal("show");
        }

        function tableString(applyLeave) {
            let noOfDays = calculateNumberOfDays(applyLeave.leave_from, applyLeave.leave_upto);
            let approval = approved_status_html(applyLeave.status);
            let statusNote = "";
            if (applyLeave.status == 1 || applyLeave.status == '1') {
                statusNote = `
                <div>
                    <h4 class="mt-4">Declined Note</h4>
                    <p>${applyLeave.note}</p>
                </div>
                `;
            }

            let resultBody = addBrEvery6Words(applyLeave.body);
            console.log("Result Body");
            console.log(resultBody);
            console.log(applyLeave.leave_from);
            let parts = applyLeave.leave_from.split('/');
            let parts2 = applyLeave.leave_upto.split('/');
            let new_leave_from = `${parts[1]}/${parts[0]}/${parts[2]}`;
            let new_leave_upto = `${parts2[1]}/${parts2[0]}/${parts2[2]}`;
            console.log(parts);
            let tablestring = `<div class="table-responsive"><table class="table table-striped custom-table mb-0 employeeapplicationtable">
                <thead>
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Title</th>
                        <th scope="col">From</th>
                        <th scope="col">To</th>
                        <th scope="col">Reason</th>
                        <th scope="col" class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>${applyLeave.id}</td>
                        <td>${applyLeave.subject}</td>
                        <td>${new_leave_from}</td>
                        <td>${new_leave_upto}</td>
                        <td>${resultBody}</td>
                        <td class="text-center">
                            ${approval}
                        </td>
                    </tr>
                </tbody>
            </table></div>
            ${statusNote}
            `;
            return tablestring;
        }

        function addBrEvery6Words(inputString) {
            // Split the input string into an array of words
            let wordsArray = inputString.split(/\s+/);

            // Insert <br> tags every 6 words
            if (wordsArray.length > 1) {
                for (let i = 6; i < wordsArray.length; i += 7) {
                    wordsArray.splice(i, 0, '<br>');
                }
            } else {
                wordsArray = inputString.split("");
                for (let i = 6; i < wordsArray.length; i += 20) {
                    wordsArray.splice(i, 0, '<br>');
                }
            }

            // Join the array back into a string
            const resultString = wordsArray.join(' ');

            return resultString;
        }

        function leaveadjust(applyLeave) {
            // adjust_leaves
            console.warn(applyLeave);
            let modalData = document.getElementById("view_modal_data");
            let imgRoute = "{{ asset('images/leaves/') }}/";
            let adjust_leave = applyLeave.adjust_leaves;
            console.log(applyLeave.adjust_leaves[0].leave_date);
            let leaveAdjustString = `
            <h4 class="mt-4">Adjust Leaves</h4>
            <table class="table table-striped custom-table mb-0 employeeapplicationtable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Leave Plan</th>
                    </tr>
                </thead>
                    <tbody>

                `;
            for (let index = 0; index < applyLeave.adjust_leaves.length; index++) {
                if (applyLeave.adjust_leaves[index].leave_plan.unit_id == 1) {
                    leaveAdjustString += `<tr>
                    <td>${adjust_leave[index].leave_date}</td>
                    <td>${adjust_leave[index].leave_plan.title}</td>
                    <td>${adjust_leave[index].quantity} Days</td>
                    </tr>
                    `;
                } else {
                    leaveAdjustString += `<tr>
                    <td>${adjust_leave[index].leave_date}</td>
                    <td>${adjust_leave[index].leave_plan.title}</td>
                    <td>${adjust_leave[index].quantity} Hours</td>
                    </tr>
                    `;
                }
            }
            let docString = "";
            if (applyLeave.document != null && applyLeave.document != "null" && applyLeave.document != "") {
                docString =
                    `<a href="${imgRoute + applyLeave.document}" class="btn btn-primary w-100 mt-4" download="${applyLeave.document}">Download Document</a>`;
            }
            leaveAdjustString += `</tbody></table>
            ${docString}
            `;
            modalData.innerHTML += leaveAdjustString;
            // return leaveAdjustString;
        }



        function approved_status_html(status) {
            if (status == '2') {
                return `<div class="action-label"> <a class = "btn btn-white btn-sm btn-rounded" href = "javascript:void(0);" >
                <i class = "fa fa-dot-circle-o text-success"></i> Approved </a> </div>`;
            } else if (status == '1') {
                return `<div class="action-label"> <a class = "btn btn-white btn-sm btn-rounded" href = "javascript:void(0);" >
                <i class = "fa fa-dot-circle-o text-danger"></i> Declined </a> </div>`;
            } else {
                return `<div class="action-label"> <a class = "btn btn-white btn-sm btn-rounded" href = "javascript:void(0);" >
                <i class = "fa fa-dot-circle-o text-primary"></i> Pending </a> </div>`;
            }
        }
    </script>
@endpush
