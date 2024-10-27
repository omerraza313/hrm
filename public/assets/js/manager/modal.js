$(document).ready(function () {
    // Code to run when the page is loaded
    $("#edit_employee").modal("show");
    let route_field = document.getElementById("route");

    let form = document.getElementById("editForm");
    form.setAttribute("action", route_field.value);
});

// $(document).ready(function () {
//     // Code to run when the page is loaded
//     var departmentId = $("#edit_department").val();
//     if (departmentId) {
//         $.get("/get-designation/" + departmentId, function (data) {
//             console.clear();
//             console.log(data);
//             $("#edit_designation").empty();
//             $("#edit_designation").append(
//                 $("<option>").text("Select Designation").val("")
//             ); // Clear existing options
//             $.each(data.data.designations, function (key, value) {
//                 $("#edit_designation").append(
//                     $("<option>").text(value.name).val(value.id)
//                 );
//             });
//         });
//     } else {
//         $("#edit_designation").empty();
//         $("#edit_designation").append(
//             $("<option>").text("Select Designation").val("")
//         ); // Clear options if no department selected
//     }
// });
