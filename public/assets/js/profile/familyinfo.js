// Delete Modal Js

function openDeleteModal(route) {
    let modalId = "#delete_family_info";
    $(modalId).modal("show");
    let form = document.getElementById("deleteForm");
    form.setAttribute("action", route);
}

// Edit Model Js

function openEditModal(route, data) {
    let familyContact = JSON.parse(data);
    console.clear();
    console.warn(familyContact);
    let form = document.getElementById("edit_family_contact_form");
    form.setAttribute("action", route);

    let routefield = document.getElementById("edit_family_route");
    let name = document.getElementById("family_edit_name");
    // let relation = document.getElementById("family_edit_relation");
    let dob = document.getElementById("family_edit_dob");
    let phone = document.getElementById("family_edit_phone");
    // alert(familyContact.relation);
    routefield.value = route;
    name.value = familyContact.name;
    // relation.value = familyContact.relation;

    // Initialize Select2
    // $('#family_modal_select').select2();

    // Set the value of the select element using Select2 API
    // $('#family_modal_select').val(familyContact.relation).trigger('change');

    $(".select").val(familyContact.relation).trigger("change.select2");
    dob.value = familyContact.dob;
    phone.value = familyContact.number;

    $("#edit_family_info_modal").modal("show");
}
