<script>
    Pusher.logToConsole = true;

    var pusher = new Pusher('5d2cb2457a70d5c0f54e', {
        cluster: 'ap2'
    });

    var channel = pusher.subscribe('employee-channel');
    channel.bind('apply-leave', function(data) {
        // alert(JSON.stringify(data));
        console.clear();
        console.log(data);

        let employee = data.employee;
        let applyLeave = data.applyLeave;
        let admin = data.admin;

        if (employee.id == '{{ auth()->user()->id }}') {
            let dataTable = $('.employeeapplicationtable').DataTable();
            let targetId = applyLeave.id;

            let targetRow = null;
            dataTable.rows().nodes().each(function(row, index) {
                let rowData = dataTable.row(row).data();
                // console.log(rowData);
                if (rowData[0] == targetId) {
                    targetRow = row;
                    return false; // Exit the loop once the target row is found
                }
            });

            if (applyLeave.status == '2') {

                if (targetRow) {
                    console.warn(targetRow.children);
                    let mainTr = targetRow;
                    mainTr.style.background = 'lightgreen';
                    let status = targetRow.children[6];
                    status.innerHTML = approved_status_html(applyLeave.status);
                    let adminName = targetRow.children[7];
                    adminName.innerHTML = admin_name_table(admin.first_name + admin.last_name);
                    let action = targetRow.children[8];


                    let ActionString = `
                    <div class="text-end">
                        <div class="dropdown dropdown-action">
                            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false"><i class="material-icons">more_vert</i></a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="#"
                                    onclick='viewLeaveFunction(${JSON.stringify(applyLeave)});'><i
                                        class="fa fa-pencil m-r-5"></i> View</a>
                            </div>
                        </div>
                    </div>
                    `;



                    action.innerHTML = ActionString;

                }
                butterup.toast({
                    title: 'Leave Approval Notification',
                    message: `Your Leave Aprroved Successfully`,
                    type: 'success',
                    icon: true, // default: false
                    location: 'bottom-right',
                });
            } else {
                if (targetRow) {
                    console.warn(targetRow.children);
                    let mainTr = targetRow;
                    mainTr.style.background = 'lightcoral';
                    let status = targetRow.children[6];
                    status.innerHTML = approved_status_html(applyLeave.status);
                    let adminName = targetRow.children[7];
                    adminName.innerHTML = admin_name_table(admin.first_name + admin.last_name);
                    let action = targetRow.children[8];

                    console.clear();
                    console.log(applyLeave);
                    console.log("new line");
                    let jsonString = JSON.stringify(applyLeave);
                    console.log(JSON.stringify(applyLeave));
                    let ActionString = `
                    <div class="text-end">
                        <div class="dropdown dropdown-action">
                            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false"><i class="material-icons">more_vert</i></a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="#"
                                    onclick='viewLeaveFunction(${jsonString});'><i
                                        class="fa fa-pencil m-r-5"></i> View</a>
                            </div>
                        </div>
                    </div>
                    `;
                    $(action).html(ActionString);
                    // action.innerHTML = ActionString;

                }
                butterup.toast({
                    title: 'Leave Approval Notification',
                    message: `Your Leave Declined Successfully`,
                    type: 'error',
                    icon: true, // default: false
                    location: 'bottom-right',
                    toastLife: '7000'
                });
            }
        }
    });

    // Admin Apply the leave for the employee
    channel.bind('admin-apply-leave', function(data) {
        // alert(JSON.stringify(data));
        // console.clear();
        // console.log(data);
        // assignLeaves
        let employee = data.employee;
        let applyLeave = data.applyLeave;
        let admin = data.admin;
        let employeeStats = data.assignLeaves;
        if (employee.id == '{{ auth()->user()->id }}') {
            // employeeapplicationtable
            let new_leave = data.apply_leave;
            let table = $('.employeeapplicationtable').DataTable();
            if ($.fn.DataTable.isDataTable('.employeeapplicationtable')) {
                table.destroy();
            }

            table = $('.employeeapplicationtable').DataTable({
                order: [
                    [0, 'desc'],
                ],
                columns: [{
                        data: 'Id'
                    },
                    {
                        data: 'Title'
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
                        data: 'Approved By'
                    },
                    {
                        data: 'Actions'
                    }
                ]
            });

            let ID = new_leave.id;
            let Title = new_leave.subject;
            let From = dateFormat('j M Y', new_leave.leave_from);
            let To = dateFormat('j M Y', new_leave.leave_upto);
            let noOfDays = calculateNumberOfDays(new_leave.leave_from, new_leave.leave_upto);
            let Reason = new_leave.body;
            let Status = `<div class="text-center"><div class="action-label"> <a class = "btn btn-white btn-sm btn-rounded" href = "javascript:void(0);" >
                <i class = "fa fa-dot-circle-o text-primary"></i> Pending </a></div></div>`;
            let approvedBy = `<h2 class="table-avatar">
                    <a href="#" class="avatar avatar-xs"><img
                            src="{{ asset('assets/img/profiles/avatar-09.jpg') }}" alt=""></a>
                    <a href="#">Waiting</a>
                </h2>`;
            let Actions = `<div class="text-end"><div class="dropdown dropdown-action">
                                <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                        data-bs-target="#edit_leave"><i class="fa fa-pencil m-r-5"></i> View</a>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                        data-bs-target="#delete_approve"><i class="fa fa-trash-o m-r-5"></i>
                                        Delete</a>
                                </div>
                            </div></div>`;


            let row = table.row.add({
                'Id': ID,
                'Title': Title,
                'From': From,
                'To': To,
                'No of Days': noOfDays + ' days',
                'Reason': Reason,
                'Status': Status,
                'Approved By': approvedBy,
                'Actions': Actions,
            });

            row.draw();

            $(row.node()).css('background-color', 'lightgreen');



            // Update Stats
            let stats = document.getElementById('employee_leave_stats');
            stats.innerHTML = "";
            for (let index = 0; index < employeeStats.length; index++) {
                const element = employeeStats[index];
                stats.innerHTML += `
                <div class="col-md-3">
                    <div class="stats-info">
                        <h6>${element.leave_plan.title} (Remaining)</h6>
                        <h4>${element.remaining_leave} / ${element.leave_plan.quantity}</h4>
                    </div>
                </div>
                `;
            }


            butterup.toast({
                title: 'Admin Leave Notification',
                message: `${admin.first_name} ${admin.last_name} Apply for your leave`,
                type: 'success',
                icon: true, // default: false
                location: 'bottom-right',
            });
        }
    });


    channel.bind('delete-employee-leave', function(data) {
        let employee = data.employee;
        let apply_leave_id = data.apply_leave_id;
        let leavCount = data.leave_count;
        butterup.toast({
            title: 'Employee Leave Notification',
            message: `${employee.first_name} ${employee.last_name} your leave deleted by Admin`,
            type: 'warning',
            icon: true, // default: false
            location: 'bottom-right',
        });
        let status = "{{ Route::is('employee.leave.application.view') }}";
        console.clear();
        console.log("Delete");
        console.log(typeof status);
        console.log(data);

        if (status == "1") {
            $("#leave_count").html(leavCount);
            let table = $('.employeeapplicationtable').DataTable();
            // if ($.fn.DataTable.isDataTable('.employeeapplicationtable')) {
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

    function admin_name_table(name) {
        return `<h2 class="table-avatar">
        <a href="#" class="avatar avatar-xs"><img
                src="' . $img . '" alt=""></a>
        <a href="#">${name}</a>
    </h2>`;
    }
</script>


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
