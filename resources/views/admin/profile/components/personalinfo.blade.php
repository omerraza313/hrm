<div class="card profile-box flex-fill">
    <div class="card-body">
        <h3 class="card-title">Personal Information
            @if (!$employee->deleted_at)
                <a href="#" class="edit-icon" data-bs-toggle="modal" data-bs-target="#personal_info_modal"><i
                        class="fa fa-pencil"></i></a>
            @endif
        </h3>
        <ul class="personal-info">
            <li>
                <div class="title">Pseudo Name :</div>
                <div class="text">{{ $employee->employee_details->pseudo_name }}</div>
            </li>
            <li>
                <div class="title">CNIC :</div>
                <div class="text">{{ $employee->employee_details->cnic }}</div>
            </li>
            <li>
                <div class="title">Phone :</div>
                <div class="text"><a
                        href="tel:{{ $employee->employee_details->phone }}">{{ $employee->employee_details->phone }}</a>
                </div>
            </li>
            <li>
                <div class="title">Marital Status :</div>
                <div class="text">{{ $employee->employee_details->martial_status }}</div>
            </li>
        </ul>
    </div>
</div>

@include('admin.profile.components.modals.personalinfomodal')
@php
    $eme_error_status = false;
    foreach ($errors->getMessages() as $field => $messages) {
        if (substr($field, 0, 6) == 'pinfo_') {
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

                $("#personal_info_modal").modal("show");
            });
        </script>
    @endpush
@endif
