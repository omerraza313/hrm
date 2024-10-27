function openEditProfileModal(employee) {
    var data = JSON.parse(employee);
    console.clear();
    console.warn(data);
    let modalId = "#profile_info";

    // $("#edit_department").val(data.employee_details.department_id).trigger("change.select2");

    var departmentId = $("#pro_department").val();
    /*if (departmentId) {
        $.get("/admin/get-designation/" + departmentId, function (data2) {
            console.clear();
            console.log(data2);
            $("#pro_designation").empty();
            $("#pro_designation").append(
                $("<option>").text("Select Designation").val("")
            ); // Clear existing options
            $.each(data2.data.designations, function (key, value) {
                $("#pro_designation").append(
                    $("<option>").text(value.name).val(value.id)
                );
            });
            $("#pro_designation")
                .val(data.employee_details.designation_id)
                .trigger("change.select2");
        });
    } else {
        $("#pro_designation").empty();
        $("#pro_designation").append(
            $("<option>").text("Select Designation").val("")
        ); // Clear options if no department selected
    }*/

    $(modalId).modal("show");
}

$(document).ready(function () {
    // When the department select changes, trigger the Ajax call
    $("#pro_department").on("change", function () {
        var departmentId = $(this).val();
        if (departmentId) {
            $.get("/get-designation/" + departmentId, function (data) {
                console.clear();
                console.log(data);
                $("#pro_designation").empty();
                $("#pro_designation").append(
                    $("<option>").text("Select Designation").val("")
                ); // Clear existing options
                $.each(data.data.designations, function (key, value) {
                    $("#pro_designation").append(
                        $("<option>").text(value.name).val(value.id)
                    );
                });
            });
        } else {
            $("#pro_designation").empty();
            $("#pro_designation").append(
                $("<option>").text("Select Designation").val("")
            ); // Clear options if no department selected
        }
    });
});
