<div class="card profile-box flex-fill">
    <div class="card-body">
        <h3 class="card-title">Bank Information
            @if (!$employee->deleted_at)
                <a href="#" class="edit-icon" data-bs-toggle="modal" data-bs-target="#bank_modal"><i
                        class="fa fa-pencil"></i></a>
            @endif
        </h3>
        <ul class="personal-info">
            <li>
                <div class="title">Bank Name :</div>
                <div class="text">{{ $employee->bank->name ?? 'No Bank Added' }}</div>
            </li>
            <li>
                <div class="title">Bank Account No :</div>
                <div class="text">{{ $employee->bank->account_no ?? 'No Bank Account No Added' }}</div>
            </li>
            <li>
                {{--
                    Change the branch code to title in text 2024-03-15
                    --}}
                <div class="title">Account Title :</div>
                <div class="text">{{ $employee->bank->branch_code ?? 'No Account Title Added' }}</div>
            </li>
            <li>
                <div class="title">IBAN Number :</div>
                <div class="text">{{ $employee->bank->iban_number ?? 'No IBAN Number Added' }}</div>
            </li>
        </ul>
    </div>
</div>

@include('admin.profile.components.modals.bankinfomodal')

@php
    $bank_error_status = false;
    foreach ($errors->getMessages() as $field => $messages) {
        if (substr($field, 0, 5) == 'bank_') {
            $bank_error_status = true;
            break;
        }
    }
@endphp

@if ($bank_error_status)
    @push('modal-script')
        <script>
            $(document).ready(function() {
                // Code to run when the page is loaded

                $("#bank_modal").modal("show");
            });
        </script>
    @endpush
@endif
