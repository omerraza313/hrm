<div class="card-body">
    <h3 class="card-title">Education Information
         <!-- <a href="#" class="edit-icon" data-bs-toggle="modal"
            data-bs-target="#education_info"><i class="fa fa-pencil"></i></a> -->
    </h3>
    <div class="experience-box">
        <ul class="experience-list">
            @if (isset($employee->educations) && $employee->educations)
                @foreach ($employee->educations as $education)
                    @php
                        $complete_carbonDate = \Carbon\Carbon::createFromFormat('m/d/Y', $education->complete_date);

                        $complete_year = $complete_carbonDate->year;

                        $start_carbonDate = \Carbon\Carbon::createFromFormat('m/d/Y', $education->start_date);

                        $start_year = $start_carbonDate->year;
                    @endphp
                    <li>
                        <div class="experience-user">
                            <div class="before-circle"></div>
                        </div>
                        <div class="experience-content">
                            <div class="timeline-content">
                                <a href="#/" class="name">{{ $education->name }}
                                </a>
                                <div>{{ $education->degree }}</div>
                                <span class="time">{{ $start_year }} -
                                    {{ $complete_year }}</span>
                            </div>
                        </div>
                    </li>
                @endforeach
            @endif
        </ul>
    </div>
</div>

{{-- @include('admin.profile.components.modals.addeducationmodal') --}}
