// PayRoll

function policy_payroll_setting_html(policy) {
    let pay_gen_type = get_gen_type_name(
        policy.pay_roll_settings.generation_type
    );
    let payRollDetails = get_payroll_data(policy);
    return `
        <div class="col-lg-12 mt-5">
            <h4>Payroll setting</h4>
            <table class="table table-striped mb-0">
                <tbody>
                    <tr>
                        <th>Payslip Generation Type:</th>
                        <td>${pay_gen_type}</td>
                    </tr>
                    ${payRollDetails}
                </tbody>
            </table>
        </div>
    `;
}

function get_payroll_data(policy) {
    if (policy.pay_roll_settings.generation_type == 3) {
        return `
            <tr>
                <th>Off Days Allowed Per Month: </th>
                <td>${policy.pay_roll_settings.off_days_per_month} Days</td>
            </tr>
            <tr>
                <th>Required Working Hours: </th>
                <td>${policy.pay_roll_settings.working_hours} Hours</td>
            </tr>
            <tr>
                <th>Required Minutes: </th>
                <td>${policy.pay_roll_settings.minutes} Min</td>
            </tr>
            <tr>
                <th>Max Shift Retaining Hours: </th>
                <td>${policy.pay_roll_settings.max_shift_retaining_hours} Hours</td>
            </tr>
        `;
    } else {
        return `
        <tr>
            <th>Off Days Allowed Per Month: </th>
            <td>${policy.pay_roll_settings.off_days_per_month} Days</td>
        </tr>
    `;
    }
}

function pay_gen_type() {
    return {
        // '1' : 'Time Base',
        2: "Attendance Base",
        3: "Hourly Base",
    };
}

function get_gen_type_name(index) {
    let ary = pay_gen_type();
    return ary[index];
}

// Policy Mapping String

function dptStringFun(policy) {
    let str = "";
    policy.departments.forEach((element) => {
        str += element.name + ", ";
    });

    return str.slice(0, -2);
}

function empStringFun(policy) {
    let str = "";
    policy.users.forEach((element) => {
        str += element.first_name + " " + element.last_name + ", ";
    });

    return str.slice(0, -2);
}

function policy_mapping_html(policy) {
    let dptString = dptStringFun(policy);
    let empString = empStringFun(policy);

    return `
            <div class="col-lg-12">
                <h4>Policy Mapping</h4>
                <table class="table table-striped mb-0">
                    <tbody>
                        <tr>
                            <th>Name:</th>
                            <td>${policy.policy}</td>
                        </tr>
                        <tr>
                            <th>Departments:</th>
                            <td style="white-space: normal;">${dptString}</td>
                        </tr>
                        <tr>
                            <th>Employees:</th>
                            <td style="white-space: normal;">${empString}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
    `;
}

// Working Hours

function policy_working_hours_html(policy) {
    let early_arrival_policy = get_early_arrival_policy_name(
        policy.working_settings.early_arrival_policy
    );

    let forceTimeout = get_force_timeout_name(
        policy.working_settings.force_timeout
    );

    let timeoutPolicy = get_timeout_policy_name(
        policy.working_settings.timeout_policy
    );
    return `
        <div class="col-lg-12 mt-5">
            <h4>Working Hours</h4>
            <table class="table table-striped mb-0">
                <tbody>
                    <tr>
                        <th>Shift Start Time:</th>
                        <td>${policy.working_settings.shift_start}</td>
                        <th>Shift Closing Time:</th>
                        <td>${policy.working_settings.shift_close}</td>
                    </tr>
                    <tr>
                        <th>Late Coming Leniency Time:</th>
                        <td>${policy.working_settings.late_c_l_t} Mins</td>
                        <th>Early Arrival Policy:</th>
                        <td>${early_arrival_policy}</td>
                    </tr>
                    <tr>
                        <th>Force Timeout:</th>
                        <td>${forceTimeout}</td>
                        <th>Timeout Policy:</th>
                        <td>${timeoutPolicy}</td>
                    </tr>
                    <tr>
                        <th>Late Minute Monthly Bucket:</th>
                        <td>${policy.working_settings.late_minute_monthly_bucket} Mins</td>
                        <th>Late Comers Penalty:</th>
                        <td>${policy.working_settings.late_comers_penalty} Hours</td>
                    </tr>
                </tbody>
            </table>
        </div>
    `;
}

function early_arrival_policy() {
    return {
        1: "Actual Time",
        2: "Shift Time",
    };
}

function get_early_arrival_policy_name(index) {
    let ary = early_arrival_policy();
    return ary[index];
}

function get_force_timeout() {
    return {
        1: "01 Hour",
        2: "02 Hour",
        3: "03 Hour",
        4: "04 Hour",
        5: "05 Hour",
        6: "06 Hour",
        7: "07 Hour",
        8: "08 Hour",
        9: "09 Hour",
        10: "10 Hour",
    };
}

function get_force_timeout_name(index) {
    let ary = get_force_timeout();
    return ary[index];
}

function get_timeout_policy() {
    return {
        1: "Present",
        2: "Absent",
        3: "Half Day",
        4: "One Hour",
    };
}

function get_timeout_policy_name(index) {
    let ary = get_timeout_policy();
    return ary[index];
}

// Working Days
function working_days_html(policy) {
    let list = policy.working_day;
    let mainString = `<div class="col-lg-12 mt-5">
    <h4>Working Days</h4>
            <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>Day</th>
                    <th>Start Time</th>
                    <th>Close Time</th>
                </tr>
            </thead>
            <tbody>`;
    list.forEach((element) => {
        let day = get_policy_day_name(element.day);
        let subStr = `
            <tr>
                <td>
                    ${day}
                </td>
                <td>
                    ${element.start_time}
                </td>
                <td>
                    ${element.close_time}
                </td>
            </tr>
        `;

        mainString += subStr;
    });

    mainString += `</tbody></table>
    </div>`;
    return mainString;
}

function get_policy_days() {
    return {
        1: "Monday",
        2: "Tuesday",
        3: "Wednesday",
        4: "Thursday",
        5: "Friday",
        6: "Saturday",
        7: "Sunday",
    };
}

function get_policy_day_name(index) {
    let ary = get_policy_days();
    return ary[index];
}

// OverTime Policy
function policy_overTime_html(policy) {
    let otStatus = get_ot_status_name(policy.overtime.ot_status);
    let otDetails = get_ot_details_html(policy);
    return `
    <div class="col-lg-12 mt-5">
        <h4>OverTime</h4>
        <table class="table table-striped mb-0">
            <tbody>
                <tr>
                    <th>
                        Overtime Status
                    </th>
                    <td>
                        ${otStatus}
                    </td>
                    <th></th>
                    <th></th>
                </tr>
                ${otDetails}
            </tbody>
        </table>
    </div>
    `;
}

function get_ot_details_html(policy) {
    if (policy.overtime.ot_status == 2) {
        let rateStatus = get_over_time_rate_name(
            policy.overtime.ot_rate_status
        );

        let rateHtml = "";
        if (policy.overtime.ot_rate_status == 1) {
            rateHtml = `
                <tr>
                    <th>
                        Overtime Rate
                    </th>
                    <td>
                        ${policy.overtime.ot_rate}
                    </td>
                    <th>
                        Overtime Amount
                    </th>
                    <td>
                        ${policy.overtime.ot_amount}
                    </td>
                </tr>
            `;
        } else if (policy.overtime.ot_rate_status == 3) {
            rateHtml = `
            <tr>
                <th>
                    Overtime Rate
                </th>
                <td>
                    ${policy.overtime.ot_rate}
                </td>
            </tr>
        `;
        } else {
            rateHtml = ``;
        }
        return `
        <tr>
            <th>
                Overtime starts after following min of duty closing:
            </th>
            <td>
                ${policy.overtime.ot_start}
            </td>
            <th>
                Minimum minute(s) required for overtime:
            </th>
            <td>
                ${policy.overtime.ot_min_minutes}
            </td>
        </tr>
        <tr>
            <th>
                Overtime Rate:
            </th>
            <td>
                ${rateStatus}
            </td>
            <th></th>
            <th></th>
        </tr>
        ${rateHtml}
        `;
    } else {
        return "";
    }
}

function get_over_time_status() {
    return {
        1: "Unpaid",
        2: "Paid",
    };
}

function get_ot_status_name(index) {
    let ary = get_over_time_status();
    return ary[index];
}

function get_over_time_rate() {
    return {
        1: "Fixed Rate/hour",
        2: "Equal Salary/hour",
        3: "Salary/Hour Multiply X",
        4: "Equal Salary/Day",
    };
}

function get_over_time_rate_name(index) {
    let ary = get_over_time_rate();
    return ary[index];
}

// Holiday OverTime
function policy_holiday_overTime_html(policy) {
    let hOtStatus = get_holiday_over_time_rate_name(
        policy.holiday_overtime.holiday_ot_status
    );

    let values_html = get_holiday_over_time_values(policy);

    let mainString = `
    <div class="col-lg-12 mt-5">
        <h4>Holiday OverTime</h4>
        <table class="table table-striped mb-0">
            <tbody>
                <tr>
                    <th>
                        Holiday/Weekend Overtime
                    </th>
                    <td>
                        ${hOtStatus}
                    <td>
                    <th></th>
                    <th></th>
                </tr>
                ${values_html}
            </tbody>
        </table>
    </div>
    `;

    return mainString;
}

function get_holiday_over_time_values(policy) {
    if (
        policy.holiday_overtime.holiday_ot_status == 3 ||
        policy.holiday_overtime.holiday_ot_status == 4
    ) {
        return `
        <tr>
            <th>
                Overtime Rate
            </th>
            <td>
                ${policy.holiday_overtime.holiday_ot_rate}
            </td>
            <th>
                Overtime Amount
            </th>
            <td>
                ${policy.holiday_overtime.holiday_ot_amount}
            </td>
        </tr>
        `;
    } else if (policy.holiday_overtime.holiday_ot_status == 5) {
        return `
        <tr>
            <th>
                Overtime Rate
            </th>
            <td>
                ${policy.holiday_overtime.holiday_ot_rate}
            </td>
        </tr>
        `;
    } else {
        return ``;
    }
}

function get_holiday_over_time_rate() {
    return {
        1: "Unpaid",
        2: "Equal Salary/hour",
        3: "Fixed Rate/hour",
        4: "Fixed Rate/day",
        5: "Salary/Hour Multiply X",
        6: "Equal Salary/Day",
    };
}

function get_holiday_over_time_rate_name(index) {
    let ary = get_holiday_over_time_rate();
    return ary[index];
}

// Assign Policy Modal
async function openAssignModal(policy) {
    let modalId = "#assign_policy";
    $(modalId).modal("show");
    get_employees_options(policy);
    get_departments_options(policy);
    // let employee_options = await get_employees_options(policy);
    $("#assing_policy_data").html(`
        <div class="row">
            <div class="col-lg-12">
                <h4>Assign Policy</h4>
                <table class="table table-striped mb-0">
                    <tbody>
                        <tr>
                            <th>Name:</th>
                            <td>${policy.policy}</td>
                        </tr>
                        <tr>
                            <th>Departments:</th>
                            <td style="white-space: normal;">${dptStringFun(
                                policy
                            )}</td>
                        </tr>
                        <tr>
                            <th>Employees:</th>
                            <td style="white-space: normal;">${empStringFun(
                                policy
                            )}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-12 mt-3">
                <form method="POST" action="/admin/assign-policy">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="col-form-label">Department</label>
                                <select class="select" name="department_ids[]" multiple="multiple" id="assign_policy_department">

                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <input type="hidden" name="policy_id" value="${
                                policy.id
                            }">
                            <div class="form-group">
                                <label class="col-form-label">Employee</label>
                                <select class="select" name="employee_ids[]" multiple="multiple" id="assign_policy_employee">

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <input type="submit" class="btn btn-primary" value="Assign Policy">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    `);
}

async function get_employees_options(policy) {
    console.clear();
    console.log(policy);
    let departments = policy.departments;
    let employees = await get_employees_from_dept(departments, policy.id);
    // let options = "";
    var select2Instance = $("#assign_policy_employee").select2({
        minimumResultsForSearch: -1,
        width: "100%",
    });
    for (let i = 0; i < employees.length; i++) {
        // options += `<option value="${employees[i].id}">${employees[i].first_name} ${employees[i].last_name}</option>`;

        // Get the Select2 instance for the select element
        // console.log(select2Instance);
        // Create a new option element
        let emp_status = false;
        for (let j = 0; j < policy.users.length; j++) {
            if (policy.users[j].id == employees[i].id) {
                emp_status = true;
            }
        }
        if (emp_status == true) {
            var newOption = new Option(
                `${employees[i].first_name} ${employees[i].last_name}`,
                employees[i].id,
                false,
                true
            );
        } else {
            var newOption = new Option(
                `${employees[i].first_name} ${employees[i].last_name}`,
                employees[i].id,
                false,
                false
            );
        }

        // Append the new option to the select2 dropdown
        select2Instance.append(newOption);

        // Trigger the 'change' event to notify Select2 about the change
        select2Instance.trigger("change");
    }

    // return options;
}

async function get_employees_from_dept(deptIds, policy_id) {
    console.log(deptIds);
    let jsonDept = JSON.stringify(deptIds);
    let host = window.location.origin;
    console.log(host);
    let url = host + "/admin/get/employee/department";
    let employees = [];

    try {
        const response = await $.ajax({
            url: url,
            type: "POST",
            data: {
                department_ids: jsonDept,
                policy_id: policy_id,
            },
            dataType: "json",
        });

        console.log(response);
        employees = response.data;
    } catch (error) {
        console.error("Error fetching employees:", error);
    }

    return employees;
}

async function get_departments_options(policy) {
    console.clear();
    console.log(policy);
    let departments = policy.departments;
    let departments_list = await get_dept_from_polices(departments, policy.id);
    console.log(departments_list);
    // let options = "";
    var select2Instance = $("#assign_policy_department").select2({
        minimumResultsForSearch: -1,
        width: "100%",
    });
    for (let i = 0; i < departments_list.length; i++) {
        // options += `<option value="${departments_list[i].id}">${departments_list[i].first_name} ${departments_list[i].last_name}</option>`;

        // Get the Select2 instance for the select element
        // console.log(select2Instance);
        // Create a new option element
        let emp_status = false;
        for (let j = 0; j < policy.departments.length; j++) {
            if (policy.departments[j].id == departments_list[i].id) {
                emp_status = true;
            }
        }
        if (emp_status == true) {
            var newOption = new Option(
                `${departments_list[i].name}`,
                departments_list[i].id,
                false,
                true
            );
        } else {
            var newOption = new Option(
                `${departments_list[i].name}`,
                departments_list[i].id,
                false,
                false
            );
        }

        // Append the new option to the select2 dropdown
        select2Instance.append(newOption);

        // Trigger the 'change' event to notify Select2 about the change
        select2Instance.trigger("change");
    }

    // return options;
}

async function get_dept_from_polices(deptIds, policy_id) {
    console.log(deptIds);
    let jsonDept = JSON.stringify(deptIds);
    let host = window.location.origin;
    console.log(host);
    let url = host + "/admin/get-departments";
    let employees = [];

    try {
        const response = await $.ajax({
            url: url,
            type: "POST",
            data: {
                department_ids: jsonDept,
                policy_id: policy_id,
            },
            dataType: "json",
        });

        console.log(response);
        employees = response.data;
    } catch (error) {
        console.error("Error fetching employees:", error);
    }

    return employees;
}
