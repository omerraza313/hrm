<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            {{-- employeeapplicationtable --}}
            <table class="table table-striped custom-table mb-0 employeeapplicationtable">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Subject</th>
                        <th>From</th>
                        <th>To</th>
                        <th>No of Days/Hoursa</th>
                        {{-- <th>Reason</th> --}}
                        <th>Type</th>
                        <th class="text-center">Status</th>
                        <th>Approved by</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($applyLeaves)
                        @foreach ($applyLeaves as $key => $applyLeave)
                            @php
                                $leave_from_parts = explode('/', $applyLeave->leave_from);
                                $leave_upto_parts = explode('/', $applyLeave->leave_upto);
                                $applyLeave->leave_from = $leave_from_parts[1].'/'.$leave_from_parts[0].'/'.$leave_from_parts[2];
                                $applyLeave->leave_upto = $leave_upto_parts[1].'/'.$leave_upto_parts[0].'/'.$leave_upto_parts[2];
                            @endphp
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>{{ $applyLeave->subject }}</td>
                                <td>{{ $applyLeave->leave_from }}</td>
                                <td>{{ $applyLeave->leave_upto }}</td>
                                {{-- <td>{{ \App\Helpers\DateHelper::calculateNumberOfDays($applyLeave->leave_from, $applyLeave->leave_upto) }}
                                    Days
                                </td> --}}
                                <td>
                                    @php
                                        $noOfDays = 0;
                                        $noOfHours = 0;
                                        foreach ($applyLeave->adjust_leaves as $adjust_leave) {
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
                                {{-- <td>{{ Str::limit($applyLeave->body, 10) }}....</td> --}}
                                <td style="white-space: normal !important">
                                    @foreach ($applyLeave->adjust_leaves as $adLeave)
                                        <span class="badge bg-success">{{ $adLeave->leave_plan->title }}</span>
                                    @endforeach
                                </td>
                                <td class="text-center">
                                    {!! \App\Helpers\ApprovedHelper::status_html($applyLeave->status) !!}
                                </td>
                                <td>
                                    {!! \App\Helpers\ApprovedHelper::approved_html($applyLeave->approved_by) !!}
                                </td>
                                <td class="text-end">

                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                                            aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="#"
                                                onclick="viewLeaveFunction('{{ $applyLeave }}');"><i
                                                    class="fa fa-pencil m-r-5"></i>
                                                View</a>
                                            @if (!$applyLeave->approved_by)
                                                <a class="dropdown-item" href="#"
                                                    onclick="deleteLeaveFunction('{{ route('employee.leave.delete', $applyLeave->id) }}');"><i
                                                        class="fa fa-trash-o m-r-5"></i>
                                                    Delete</a>
                                            @endif
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
@include('employee.application.leave.components.modals.deletemodal')
@include('employee.application.leave.components.modals.viewleave')
@push('modal-script')
    <script>
        $('.employeeapplicationtable').DataTable({
            order: [
                [0, 'desc']
            ],
            // columnDefs: [{
            //         width: '100px',
            //         targets: 5
            //     } // Set width of the first column to 200px
            // ]
        });

        function deleteLeaveFunction(route) {

            let modalId = "#delete_leave";
            $(modalId).modal("show");
            let form = document.getElementById("deleteForm");
            form.setAttribute("action", route);
            // delete_leave
        }


        // View Modal

        function viewLeaveFunction(applyLeave) {
            //
            console.log("view");
            console.log(applyLeave);
            console.log(typeof applyLeave);
            if (typeof applyLeave != 'object') {
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
            let tablestring = `<div class="table-responsive"><table class="table table-striped custom-table mb-0 employeeapplicationtable">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Title</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Reason</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>${applyLeave.id}</td>
                        <td>${applyLeave.subject}</td>
                        <td>${applyLeave.leave_from}</td>
                        <td>${applyLeave.leave_upto}</td>
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
            let leaveAdjustString = `
            <h4 class="mt-4">Adjust Leaves</h4>
            <table class="table table-striped custom-table mb-0 employeeapplicationtable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Leave Plan</th>
                        <th>No of Days/Hours</th>
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
                    ` <a href="${imgRoute + applyLeave.document}"
                class="btn btn-primary w-100 mt-4"
                download="${applyLeave.document}">Download Document</a>`;
            }
            leaveAdjustString += `</tbody></table>
                ${docString}
                `;
            modalData.innerHTML += leaveAdjustString;
            // return leaveAdjustString;
        }
    </script>
@endpush
