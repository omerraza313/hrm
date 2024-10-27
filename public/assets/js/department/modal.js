$(document).ready(function() {
    // Show the modal on page load
    let edit_name_field = document.getElementById('edit_name');
    let route_field = document.getElementById('route');

    $("#edit_department").modal('show');
    let form = document.getElementById('editForm');
    form.setAttribute('action', route_field.value);
});
