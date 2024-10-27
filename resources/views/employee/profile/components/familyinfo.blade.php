<div class="card-body">
    <h3 class="card-title">Family Information 
        <!-- <a href="#" class="edit-icon" data-bs-toggle="modal"
            data-bs-target="#family_info_modal"><i class="fa fa-pencil"></i></a> -->
        </h3>
    <div class="table-responsive">
        <table class="table table-nowrap">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Relationship</th>
                    <th>Date of Birth</th>
                    <th>Phone</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @if (isset($employee->family_contacts) && $employee->family_contacts)
                    @foreach ($employee->family_contacts as $family_contact)
                        <tr>
                            <td>{{ $family_contact->name }}</td>
                            <td>{{ $family_contact->relation }}</td>
                            <td>{{ $family_contact->dob }}</td>
                            <td>{{ $family_contact->number }}</td>
                            <td class="text-end">
                                <div class="dropdown dropdown-action">
                                    <a aria-expanded="false" data-bs-toggle="dropdown"
                                        class="action-icon dropdown-toggle" href="#"><i
                                            class="material-icons">more_vert</i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="#" class="dropdown-item"
                                            onclick="openEditModal('{{ route('familycontact.update', $family_contact->id) }}', '{{ json_encode($family_contact) }}');"><i
                                                class="fa fa-pencil m-r-5"></i>
                                            Edit</a>
                                        <a href="#" class="dropdown-item"
                                            onclick="openDeleteModal('{{ route('familycontact.delete', $family_contact->id) }}');"><i
                                                class="fa fa-trash-o m-r-5"></i>
                                            Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>

{{-- @include('admin.profile.components.modals.familyinfomodal')
@include('admin.profile.components.modals.deletefamilyinfomodal')
@include('admin.profile.components.modals.editfamilyinfomodal') --}}

@php
    $famiily_error_status = false;
    $edit_famiily_error_status = false;
    foreach ($errors->getMessages() as $field => $messages) {
        if (substr($field, 0, 11) == 'family_add_') {
            $famiily_error_status = true;
            break;
        }
        if (substr($field, 0, 12) == 'family_edit_') {
            $edit_famiily_error_status = true;
            break;
        }
    }
@endphp

@if ($famiily_error_status)
    @push('modal-script')
        <script>
            $(document).ready(function() {
                // Code to run when the page is loaded

                $("#family_info_modal").modal("show");
            });
        </script>
    @endpush
@endif

@push('modal-script')
    <script src="{{ asset('assets/js/profile/familyinfo.js') }}"></script>
@endpush



@if ($edit_famiily_error_status)
    @push('modal-script')
        <script>
            $(document).ready(function() {
                // Show the modal on page load
                let route_field = document.getElementById('edit_family_route');

                $("#edit_family_info_modal").modal('show');
                let form = document.getElementById('edit_family_contact_form');
                form.setAttribute('action', route_field.value);
            });
        </script>
    @endpush
@endif
