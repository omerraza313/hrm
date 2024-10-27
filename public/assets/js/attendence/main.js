// function modalViewfunc(data) {
//     console.clear();
//     console.log("open edit option");
//     console.log(data);
//     let mainDate = data.a_date;
//     // let userDetail =
//     //     data.user.first_name +
//     //     " " +
//     //     data.user.last_name +
//     //     " <small style='color : #4e4e4e; font-size: 10px;'>(" +
//     //     data.user.employee_details.designation.name +
//     //     ")</small>";
//     let userDetail = data.user.first_name + " " + data.user.last_name;

//     let arrivalTime = timeField(
//         timeFormat(data.data.arrival_time),
//         "arrival_time"
//     );
//     let leaveTime = timeField(timeFormat(data.data.leave_time), "leave_time");
//     let PolicyName = data.data.policy.policy;
//     let status = getStatusView(data);
//     let viewString = `
//     <div class="table-responsive">
//         <input type="hidden" name="id" value="${data.data.id}" />
//         <table class="table table-striped custom-table mb-0">
//             <thead>
//                 <tr>
//                     <th>Date </th>
//                     <th>User details</th>
//                     <th>Arrival Time</th>
//                     <th>Leave Time</th>
//                     <th>Policy Name</th>
//                     <th>Status</th>
//                 </tr>
//             </thead>
//             <tbody>
//                     <tr>
//                         <td>${mainDate}</td>
//                         <td>${userDetail}</td>
//                         <td>${arrivalTime}</td>
//                         <td>${leaveTime}</td>
//                         <td>${PolicyName}</td>
//                         <td>${status}</td>
//                     </tr>
//             </tbody>
//         </table>
//     </div>
//     `;

//     return viewString;
// }
function timeField(time, name) {
    return `
    <input type="text" name="${name}" class="form-control timepicker" value="${time}" />
    `;
}
function timeFormat(mainDate) {
    var date = new Date(mainDate);
    // Format the time as h:i A
    var hours = ("0" + (date.getHours() % 12) || 12).slice(-2);
    var minutes = ("0" + date.getMinutes()).slice(-2);

    var ampm = date.getHours() >= 12 ? "PM" : "AM";

    return hours + ":" + minutes + " " + ampm;
}

function getStatusView(data) {
    let status = data.status;

    // let mainString = `
    // <div class="form-group">
    // <select class="form-control" name="status">
    // `;

    // let ary = [0, 1, 2, 3, 4];
    // for (let index = 0; index < ary.length; index++) {
    //     const element = ary[index];
    //     if (element == 0) {
    //         if (element == status) {
    //             mainString += `<option value="${element}" selected>Late</option>`;
    //         } else {
    //             mainString += `<option value="${element}">Late</option>`;
    //         }
    //     } else if (element == 1) {
    //         if (element == status) {
    //             mainString += `<option value="${element}" selected>On Time</option>`;
    //         } else {
    //             mainString += `<option value="${element}">On Time</option>`;
    //         }
    //     } else if (element == 2) {
    //         if (element == status) {
    //             mainString += `<option value="${element}" selected>Holiday</option>`;
    //         } else {
    //             mainString += `<option value="${element}">Holiday</option>`;
    //         }
    //     } else if (element == 3) {
    //         if (element == status) {
    //             mainString += `<option value="${element}" selected>Absent</option>`;
    //         } else {
    //             mainString += `<option value="${element}">Absent</option>`;
    //         }
    //     } else if (element == 4) {
    //         if (element == status) {
    //             mainString += `<option value="${element}" selected>Leave</option>`;
    //         } else {
    //             mainString += `<option value="${element}">Leave</option>`;
    //         }
    //     }
    // }
    // mainString += `</select>
    // </div>`;

    let mainString = `
    <div>

    `;

    let ary = [0, 1, 2, 3, 4];
    for (let index = 0; index < ary.length; index++) {
        const element = ary[index];
        if (element == 0) {
            if (element == status) {
                mainString += `<span class="alert alert-primary">Late</span>`;
            }
        } else if (element == 1) {
            if (element == status) {
                mainString += `<span class="alert alert-success">On Time</span>`;
            }
        } else if (element == 2) {
            if (element == status) {
                mainString += `<span class="alert alert-warning">Holiday</span>`;
            }
        } else if (element == 3) {
            if (element == status) {
                mainString += `<span class="alert alert-danger">Absent</span>`;
            }
        } else if (element == 4) {
            if (element == status) {
                mainString += `<span class="alert alert-info">Leave</span>`;
            }
        }
    }
    mainString += `</div>`;

    return mainString;
}

function modalViewfunc(data) {
    console.clear();
    console.log("open edit option");
    console.log(data);
    let mainDate = data.a_date;

    let tableRows = get_table_rows(data);
    let userDetail = data.user.first_name + " " + data.user.last_name;

    let arrivalTime = timeField(
        timeFormat(data.arrival_time),
        "arrival_time"
    );
    let leaveTime = timeField(timeFormat(data.leave_time), "leave_time");
    let PolicyName = data.policy;
    let status = getStatusView(data);
    let viewString = `
    <div class="table-responsive">
        <input type="hidden" name="id" value="${data.id}" />
        <table class="table table-striped custom-table mb-0">
            <thead>
                <tr>
                    <th>Date </th>
                    <th>User details</th>
                    <th>Arrival Time</th>
                    <th>Leave Time</th>
                    <th>Policy Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                ${tableRows}
            </tbody>
        </table>
    </div>
    `;

    return viewString;
}

function get_table_rows(data) {
    console.log("rows data");
    // console.log(data);
    let mainDate = data.a_date;
    let userDetail = data.user.first_name + " " + data.user.last_name;
    // let PolicyName = data.data.policy.policy;

    let mainString = "";
    let rows = data.logs;
    if (rows.length > 0) {
        for (let index = 0; index < rows.length; index++) {
            const element = rows[index];
            console.log(element);

            var utcDateCheckIn = new Date(element["arrival_time"]);
            let newyorkCheckIn = new Intl.DateTimeFormat("en-US", {
                timeZone: "America/New_York",
                year: "numeric",
                month: "2-digit",
                day: "2-digit",
                hour: "2-digit",
                minute: "2-digit",
                second: "2-digit",
            }).format(utcDateCheckIn);

            let arrivalTime = timeField(
                timeFormat(newyorkCheckIn),
                "arrival_time[]"
            );

            var utcDateCheckOut = new Date(element["leave_time"]);
            let newyorkCheckOut = new Intl.DateTimeFormat("en-US", {
                timeZone: "America/New_York",
                year: "numeric",
                month: "2-digit",
                day: "2-digit",
                hour: "2-digit",
                minute: "2-digit",
                second: "2-digit",
            }).format(utcDateCheckOut);

            let leave_time = timeField(
                timeFormat(newyorkCheckOut),
                "leave_time[]"
            );

            let status = "";
            let checkStatus = "";
            if (index == 0) {
                status = getStatusView(element);
                checkStatus = element["status"];
            }
            let policy_name = element["policy"]["policy"];
            mainString += `
                <tr>
                    <td>
                    <input type="hidden" name="id[]" value=${element["id"]} />
                    ${mainDate}</td>
                    <td>${userDetail}</td>
                    <td>${arrivalTime}</td>
                    <td>${leave_time}</td>
                    <td>${policy_name}</td>
                    <td>
                    <input type="hidden" name="status[]" value="${checkStatus}" />
                    ${status}</td>
                </tr>
            `;
        }
    }

    return mainString;
}
