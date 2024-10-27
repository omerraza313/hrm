$(document).ready(function() {
    // Show the modal on page load
    let route_field = document.getElementById('route');

    $("#edit_designation").modal('show');
    let form = document.getElementById('editForm');
    form.setAttribute('action', route_field.value);
});
