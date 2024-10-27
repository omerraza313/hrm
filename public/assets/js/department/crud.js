// edit_department_
function openEditModal(route, name, id) {
    let modalId = "#edit_department";
    let edit_name_field = document.getElementById("edit_name");
    let route_field = document.getElementById("route");
    edit_name_field.value = name;
    route_field.value = route;

    let field_error = document.getElementsByClassName("field-error");
    if (field_error != undefined && field_error.length > 0) {
        for (let index = 0; index < field_error.length; index++) {
            field_error[index].style.display = "none";
        }
    }

    $(modalId).modal("show");
    let form = document.getElementById("editForm");
    form.setAttribute("action", route);
}

function openDeleteModal(route) {
    let modalId = "#delete_department";
    $(modalId).modal("show");
    let form = document.getElementById("deleteForm");
    form.setAttribute("action", route);
}
