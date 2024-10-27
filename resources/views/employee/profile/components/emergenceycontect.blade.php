<div class="card profile-box flex-fill">
    <div class="card-body">
        <h3 class="card-title">Emergency Contact 
            <!-- <a href="#" class="edit-icon" data-bs-toggle="modal"
                data-bs-target="#emergency_contact_modal"><i class="fa fa-pencil"></i></a> -->
            </h3>
        <h5 class="section-title">Primary</h5>
        <ul class="personal-info">
            <li>
                <div class="title">Name :</div>
                <div class="text">{{ $employee->emergency_contacts->name }}</div>
            </li>
            <li>
                <div class="title">Relationship :</div>
                <div class="text">{{ $employee->emergency_contacts->relation }}</div>
            </li>
            <li>
                <div class="title">Phone :</div>
                <div class="text">{{ $employee->emergency_contacts->number }}</div>
            </li>
        </ul>
    </div>
</div>
{{-- @include('admin.profile.components.modals.emergencymodal') --}}
@php
    $eme_error_status = false;
    foreach ($errors->getMessages() as $field => $messages) {
        if (substr($field, 0, 4) == 'eme_') {
            $eme_error_status = true;
            break;
        }
    }
@endphp

@if ($eme_error_status)
    @push('modal-script')
        <script>
            $(document).ready(function() {
                // Code to run when the page is loaded

                $("#emergency_contact_modal").modal("show");
            });
        </script>
    @endpush
@endif
