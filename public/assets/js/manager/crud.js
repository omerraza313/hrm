// edit_department_
function openEditModal(route, employee, image) {
    var data = JSON.parse(employee);
    console.clear();
    console.warn(data);
    let modalId = "#edit_employee";

    let edit_first_name = document.getElementById("edit_first_name");
    let edit_last_name = document.getElementById("edit_last_name");
    let edit_email = document.getElementById("edit_email");
    let edit_employee_id = document.getElementById("edit_employee_id");
    let edit_joining_date = document.getElementById("edit_joining_date");
    let edit_phone = document.getElementById("edit_phone");
    let edit_image = document.getElementById("edit_image");
    let edit_salary = document.getElementById("edit_salary");
    let edit_blood_group = document.getElementById("edit_blood_group");

    edit_first_name.value = data.first_name;
    edit_last_name.value = data.last_name;
    edit_email.value = data.email;
    edit_employee_id.value = data.id;
    edit_joining_date.value = data.employee_details.join_date;
    edit_phone.value = data.employee_details.phone;
    edit_salary.value = data.employee_details.salary;
    edit_blood_group.value = data.employee_details.blood_group;
    edit_image.src = image;
    $("#edit_department")
        .val(data.employee_details.department_id)
        .trigger("change.select2");

    var departmentId = $("#edit_department").val();
    // if (departmentId) {
    //     $.get("/admin/get-designation/" + departmentId, function (data2) {
    //         console.clear();
    //         console.log(data2);
    //         $("#edit_designation").empty();
    //         $("#edit_designation").append(
    //             $("<option>").text("Select Designation").val("")
    //         ); // Clear existing options
    //         $.each(data2.data.designations, function (key, value) {
    //             $("#edit_designation").append(
    //                 $("<option>").text(value.name).val(value.id)
    //             );
    //         });
    //         $("#edit_designation")
    //             .val(data.employee_details.designation_id)
    //             .trigger("change.select2");
    //     });
    // } else {
    //     $("#edit_designation").empty();
    //     $("#edit_designation").append(
    //         $("<option>").text("Select Designation").val("")
    //     ); // Clear options if no department selected
    // }

    let route_field = document.getElementById("route");
    route_field.value = route;

    // $(".select").val(department_id).trigger("change.select2");

    // let field_error = document.getElementsByClassName("field-error");
    // if (field_error != undefined && field_error.length > 0) {
    //     for (let index = 0; index < field_error.length; index++) {
    //         field_error[index].style.display = "none";
    //     }
    // }

    $(modalId).modal("show");
    let form = document.getElementById("editForm");
    form.setAttribute("action", route);
}

function openDeleteModal(route) {
    let modalId = "#delete_employee";
    $(modalId).modal("show");
    let form = document.getElementById("deleteForm");
    $("#mainRoute").val(route);
    form.setAttribute("action", route);
}
// For Add Modal

// $(document).ready(function () {
//     // When the department select changes, trigger the Ajax call
//     $("#add_department").on("change", function () {
//         var departmentId = $(this).val();
//         if (departmentId) {
//             $.get("/admin/get-designation/" + departmentId, function (data) {
//                 console.clear();
//                 console.log(data);
//                 $("#add_designation").empty();
//                 $("#add_designation").append(
//                     $("<option>").text("Select Designation").val("")
//                 ); // Clear existing options
//                 $.each(data.data.designations, function (key, value) {
//                     $("#add_designation").append(
//                         $("<option>").text(value.name).val(value.id)
//                     );
//                 });
//             });
//         } else {
//             $("#add_designation").empty();
//             $("#add_designation").append(
//                 $("<option>").text("Select Designation").val("")
//             ); // Clear options if no department selected
//         }
//     });
// });

// For edit Modal

// $(document).ready(function () {
//     // When the department select changes, trigger the Ajax call
//     $("#edit_department").on("change", function () {
//         var departmentId = $(this).val();
//         if (departmentId) {
//             $.get("/admin/get-designation/" + departmentId, function (data) {
//                 console.clear();
//                 console.log(data);
//                 $("#edit_designation").empty();
//                 $("#edit_designation").append(
//                     $("<option>").text("Select Designation").val("")
//                 ); // Clear existing options
//                 $.each(data.data.designations, function (key, value) {
//                     $("#edit_designation").append(
//                         $("<option>").text(value.name).val(value.id)
//                     );
//                 });
//             });
//         } else {
//             $("#edit_designation").empty();
//             $("#edit_designation").append(
//                 $("<option>").text("Select Designation").val("")
//             ); // Clear options if no department selected
//         }
//     });
// });
