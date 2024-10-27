<div class="card-body">
    <h3 class="card-title">Experience 
        <!-- <a href="#" class="edit-icon" data-bs-toggle="modal"
            data-bs-target="#experience_info"><i class="fa fa-pencil"></i></a> -->
        </h3>
    <div class="experience-box">
        <ul class="experience-list">
            @if (isset($employee->experiences) && $employee->experiences)
                @foreach ($employee->experiences as $experience)
                    @php
                        $from_carbonDate = \Carbon\Carbon::createFromFormat('m/d/Y', $experience->from_date);

                        $from = $from_carbonDate->format('M Y');

                        $to_carbonDate = \Carbon\Carbon::createFromFormat('m/d/Y', $experience->to_date);

                        $to = $to_carbonDate->format('M Y');
                    @endphp
                    <li>
                        <div class="experience-user">
                            <div class="before-circle"></div>
                        </div>
                        <div class="experience-content">
                            <div class="timeline-content">
                                <a href="#/" class="name">{{ $experience->job_position }} at {{ $experience->company_name }}</a>
                                <span class="time">{{ $from }} - {{ $to }}</span>
                            </div>
                        </div>
                    </li>
                @endforeach
            @endif
        </ul>
    </div>
</div>

{{-- @include('admin.profile.components.modals.experiencemodal') --}}
