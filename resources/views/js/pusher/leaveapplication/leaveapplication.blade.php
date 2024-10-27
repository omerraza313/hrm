{{-- Pusher Add new record Script --}}
<script>
    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher('5d2cb2457a70d5c0f54e', {
        cluster: 'ap2'
    });

    var channel = pusher.subscribe('admin-channel');
    channel.bind('apply-leave', function(data) {
        let employee = '';
        if (data.employee) {
            employee = data.employee;
        } else {
            employee = '';
        }
        let leavCount = data.leave_count;
        butterup.toast({
            title: 'Employee Leave Notification',
            message: `${employee.first_name} ${employee.last_name} Apply for the leave`,
            type: 'success',
            icon: true, // default: false
            location: 'bottom-right',
        });
        let status = "{{ Route::is('admin.leave.application.view') }}";
        console.clear();
        console.log(typeof status);
        console.log(data);
        if (status == "1") {

            $("#leave_count").html(leavCount);
            let new_leave = data.apply_leave;
            let json_leave = data.json_apply_leave;
            let table = $('.leaveadmintable').DataTable();
            if ($.fn.DataTable.isDataTable('.leaveadmintable')) {
                table.destroy();
            }
            // location.reload();
            table = $('.leaveadmintable').DataTable({
                order: [
                    [0, 'desc'],
                ],
                columns: [{
                        data: 'Id'
                    },
                    {
                        data: 'Employee'
                    },
                    {
                        data: 'Leave Type'
                    },
                    {
                        data: 'From'
                    },
                    {
                        data: 'To'
                    },
                    {
                        data: 'No of Days'
                    },
                    {
                        data: 'Reason'
                    },
                    {
                        data: 'Status'
                    },
                    {
                        data: 'Actions'
                    }
                ]
            });
            let noOfDays = calculateNumberOfDays(new_leave.leave_from, new_leave.leave_upto);
            let fromDate = dateFormat('j M Y', new_leave.leave_from);
            let toDate = dateFormat('j M Y', new_leave.leave_upto);
            let status_string = status_dropdown(new_leave);
            let action_string = action_string_dropdown(new_leave);
            let row = table.row.add({
                Id: new_leave.id,
                Employee: `
            <h2 class="table-avatar">
                <a href="profile.html" class="avatar"><img alt=""
                        src="{{ asset('assets/img/profiles/avatar-09.jpg') }}"></a>
                <a href="#">${new_leave.employee.first_name}
                    ${new_leave.employee.last_name}
                    <span>${new_leave.employee.employee_details.designation.name}</span></a>
            </h2>
            `,
                'Leave Type': new_leave.subject,
                'From': fromDate,
                'To': toDate,
                'No of Days': noOfDays + ' days',
                'Reason': new_leave.body,
                'Status': status_string,
                'Actions': action_string,
            });

            row.draw();

            $(row.node()).css('background-color', 'lightgreen');

        }
    });

    // Delete
    channel.bind('delete-employee-leave', function(data) {
        let employee = data.employee;
        let apply_leave_id = data.apply_leave_id;
        let leavCount = data.leave_count;
        butterup.toast({
            title: 'Employee Leave Notification',
            message: `${employee.first_name} ${employee.last_name} Delete the leave`,
            type: 'warning',
            icon: true, // default: false
            location: 'bottom-right',
        });
        let status = "{{ Route::is('admin.leave.application.view') }}";
        console.clear();
        console.log(typeof status);
        console.log(data);

        if (status == "1") {
            $("#leave_count").html(leavCount);
            let table = $('.leaveadmintable').DataTable();
            // if ($.fn.DataTable.isDataTable('.leaveadmintable')) {
            //     table.destroy();
            // }

            let targetId = apply_leave_id;

            let targetRow = null;
            table.rows().nodes().each(function(row, index) {
                let rowData = table.row(row).data();
                // console.log(rowData);
                if (rowData[0] == targetId) {
                    targetRow = row;
                    return false; // Exit the loop once the target row is found
                }
            });


            if (targetRow) {
                console.warn(targetRow.children);
                let mainTr = targetRow;
                mainTr.style.background = 'lightcoral';
                mainTr.innerHTML = "Deleting";
                setTimeout(() => {
                    var rowIndex = table.rows().eq(0).filter(function(rowIndex) {
                        return table.cell(rowIndex, 0).data() == targetId;
                    });
                    table.row(rowIndex).remove().draw();
                }, 1500);
                // table.row(rowIndex).remove().draw();
            }
        }

    });

    function calculateNumberOfDays(fromDate, toDate) {
        // Parse input dates using the format 'DD/MM/YYYY'
        const fromParts = fromDate.split('/');
        const toParts = toDate.split('/');

        const fromYear = parseInt(fromParts[2], 10);
        const fromMonth = parseInt(fromParts[1], 10) - 1; // Month is zero-based
        const fromDay = parseInt(fromParts[0], 10);

        const toYear = parseInt(toParts[2], 10);
        const toMonth = parseInt(toParts[1], 10) - 1; // Month is zero-based
        const toDay = parseInt(toParts[0], 10);

        // Create Date objects
        const fromDateObj = new Date(fromYear, fromMonth, fromDay);
        const toDateObj = new Date(toYear, toMonth, toDay);

        // Calculate the difference in days (including the first date)
        const oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
        const numberOfDays = Math.round(Math.abs((toDateObj - fromDateObj) / oneDay)) + 1;

        return numberOfDays;
    }

    function dateFormat(format = 'j M Y', dateString) {
        // Parse input date using the format 'DD/MM/YYYY'
        const dateParts = dateString.split('/');
        const year = parseInt(dateParts[2], 10);
        const month = parseInt(dateParts[1], 10);
        const day = parseInt(dateParts[0], 10);

        // Create an array of month names
        const monthNames = [
            'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
        ];

        // Format the date manually
        const formattedDate = `${day} ${monthNames[month - 1]} ${year}`;

        return formattedDate;
    }

    function status_dropdown(apply_leave) {
        let mainDrop = '';
        if (apply_leave.status == 0) {
            mainDrop = `<a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#"
                        data-bs-toggle="dropdown" aria-expanded="false"><i
                            class="fa fa-dot-circle-o text-info"></i> Pending</a>`;
        } else if (apply_leave.status == 2) {
            mainDrop = `<a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#"
                        data-bs-toggle="dropdown" aria-expanded="false" data-bs-toggle="modal"
                        data-bs-target="#approve_leave"><i
                            class="fa fa-dot-circle-o text-success"></i> Approved</a>`;
        } else {
            mainDrop = `<a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#"
                        data-bs-toggle="dropdown" aria-expanded="false"><i
                            class="fa fa-dot-circle-o text-danger"></i> Declined</a>`;
        }

        let approvedRoute =
            `{{ route('admin.leave.application.update.status') }}?apply_leave_id=${apply_leave.id}&status=2`;
        let declinedRoute =
            `{{ route('admin.leave.application.update.status') }}?apply_leave_id=${apply_leave.id}&status=1`;
        let stringData = `<div class="text-center">
        <div class="dropdown action-label">
                ${mainDrop}
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="#" onclick="changeApproveStatus('Approved', '${apply_leave.id}', '${approvedRoute}');"><i
                            class="fa fa-dot-circle-o text-success"></i> Approved</a>
                    <a class="dropdown-item" href="#" onclick="changeApproveStatus('Declined', '${apply_leave.id}', '${declinedRoute}');"><i
                            class="fa fa-dot-circle-o text-danger"></i> Declined</a>
                </div>
            </div>
        </div>`;

        return stringData;
    }


    function action_string_dropdown(new_leave) {

        let deletRoute = `{{ route('admin.delete.leave.application', 1) }}`;
        let newUrl = deletRoute.slice(0, -1) + new_leave.id;
        return `
    <div class="text-end">
        <div class="dropdown dropdown-action">
            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                aria-expanded="false"><i class="material-icons">more_vert</i></a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="#"
                    onclick='viewLeaveFunction(${JSON.stringify(new_leave)});'><i
                        class="fa fa-pencil m-r-5"></i> View</a>
                <a class="dropdown-item" href="#"
                    onclick="deleteLeaveFunction('${newUrl}');"><i
                        class="fa fa-trash-o m-r-5"></i>
                    Delete</a>
            </div>
        </div>
    </div>
    `;
    }
</script>


{{-- Script for status aprroval --}}
<script>
    function changeApproveStatus(type, applyID, route) {
        let status_para = document.getElementById('status_para');
        let status_para_string = '';
        if (type == 'Pending') {
            status_para_string = `Are you sure want to pending for this leave?`;
            $('#status_btn_text').text('Pending');
            $("#d_note").html("");
        } else if (type == 'Approved') {
            status_para_string = `Are you sure want to approved for this leave?`;
            $('#status_btn_text').text('Approved');
            $("#d_note").html("");
        } else {
            status_para_string = `Are you sure want to declined for this leave?`;
            $("#d_note").html(`
                <div class="form-group">
                    <label>Note </label>
                        <textarea rows="4" name="status_note" class="form-control" placeholder="Please enter your reason"></textarea>
                </div>
            `);
            $('#status_btn_text').text('Declined');
        }
        status_para.innerText = status_para_string;

        let form = document.getElementById("statusForm");
        form.setAttribute("action", route);
        $("#approve_leave_status").modal('show');
    }
</script>
