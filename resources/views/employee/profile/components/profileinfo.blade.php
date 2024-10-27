<div class="profile-view">
    <div class="profile-img-wrap">
        <div class="profile-img">
            <a href="#"><img alt=""
                    src="{{ $employee->image ? asset('images/employee/') . '/' . $employee->image : asset('assets/img/profiles/avatar-02.jpg') }}"></a>
        </div>
    </div>
    <div class="profile-basic">
        <div class="row">
            <div class="col-md-5">
                <div class="profile-info-left">
                    <h3 class="user-name m-t-0 mb-0">
                        {{ $employee->first_name . ' ' . $employee->last_name }}</h3>
                    <h6 class="text-muted">{{ $employee->employee_details->department->name }}</h6>
                    <small class="text-muted">{{ $employee->employee_details->designation->name }}</small>
                    <div class="staff-id">Employee ID : {{ $employee->id }}</div>
                    <div class="small doj text-muted">Date of Join : {{ $employee->employee_details->join_date }}</div>
                    <div class="staff-msg"></div>
                </div>
            </div>
            <div class="col-md-7">
                <ul class="personal-info">
                    <li>
                        <div class="title">Phone :</div>
                        <div class="text"><a href="">{{ $employee->employee_details->phone }}</a></div>
                    </li>
                    <li>
                        <div class="title">Email :</div>
                        <div class="text"><a href="">{{ $employee->email }}</a></div>
                    </li>
                    <li>
                        <div class="title">Birthday :</div>
                        <div class="text">
                            {{ $employee->employee_details->dob }}
                        </div>
                        {{-- <div class="text">
                            {{ \App\Helpers\DateHelper::globaldateFormat('j M Y', $employee->employee_details->dob) }}
                        </div> --}}
                    </li>
                    <li>
                        <div class="title">Address :</div>
                        <div class="text">{{ $employee->address[0]->address }}</div>
                    </li>
                    <li>
                        <div class="title">Gender :</div>
                        <div class="text">{{ $employee->employee_details->gender }}</div>
                    </li>
                    @if ($employee->employee_details->manager)
                        <li>
                            <div class="title">Reports to :</div>
                            <div class="text">
                                <div class="avatar-box">
                                    <div class="avatar avatar-xs">
                                        <img src="{{ $employee->employee_details->manager->image ? asset('images/employee/') . '/' . $employee->employee_details->manager->image : asset('assets/img/profiles/avatar-02.jpg') }}"
                                            alt="">
                                    </div>
                                </div>
                                <a href="#">
                                    {{ $employee->employee_details->manager->first_name }}
                                    {{ $employee->employee_details->manager->last_name }}
                                </a>
                            </div>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <!-- <div class="pro-edit"><a onclick="openEditProfileModal('{{ json_encode($employee) }}')" class="edit-icon"
            href="#"><i class="fa fa-pencil"></i></a></div> -->
</div>

{{-- @include('admin.profile.components.modals.profilemodal') --}}

@push('modal-script')
    <script src="{{ asset('assets/js/profile/profile.js') }}"></script>
@endpush


@php
    $pro_error_status = false;
    foreach ($errors->getMessages() as $field => $messages) {
        if (substr($field, 0, 4) == 'pro_') {
            $pro_error_status = true;
            break;
        }
    }
@endphp

@if ($pro_error_status)
    @push('modal-script')
        <script>
            $(document).ready(function() {
                // Code to run when the page is loaded
                var departmentId = $("#pro_department").val();
                let designation_id = {{ $employee->employee_details->designation_id }};
                if (departmentId) {
                    $.get("/admin/get-designation/" + departmentId, function(data2) {
                        console.clear();
                        console.log(data2);
                        $("#pro_designation").empty();
                        $("#pro_designation").append(
                            $("<option>").text("Select Designation").val("")
                        ); // Clear existing options
                        $.each(data2.data.designations, function(key, value) {
                            $("#pro_designation").append(
                                $("<option>").text(value.name).val(value.id)
                            );
                        });
                        $("#pro_designation")
                            .val(designation_id)
                            .trigger("change.select2");
                    });
                } else {
                    $("#pro_designation").empty();
                    $("#pro_designation").append(
                        $("<option>").text("Select Designation").val("")
                    ); // Clear options if no department selected
                }
                $("#profile_info").modal("show");
            });
        </script>
    @endpush
@endif
