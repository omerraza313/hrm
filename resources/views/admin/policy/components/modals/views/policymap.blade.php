<div class="col-lg-12">
    <h4>Policy Mapping</h4>
</div>
<div class="col-lg-12">
    <div class="form-group mt-3">
        <label>Policy Name <span class="text-danger">*</span></label>
        <input class="form-control" type="text" name="add_policy_name" value="{{ old('add_policy_name', isset($policy) ? $policy->policy : '') }}" />
        <x-field-validation errorname="add_policy_name" />
    </div>
</div>
<div class="col-lg-6">
    <div class="form-group">
        <label class="col-form-label">Department</label>
        <select class="select" name="add_policy_department" onchange="departmentChange(event);">
            <option value="all" @if (old('add_policy_department', isset($policy) ? $policy->department_id : '') == 'all') selected @endif>All Departments</option>

            @if (isset($department_list))
                @foreach ($department_list as $department)
                    <option value="{{ $department->id }}" @if (old('add_policy_department') selected @endif>
                        {{ $department->name }}
                    </option>
                @endforeach
            @endif
        </select>

        <x-field-validation errorname="add_policy_department" />
    </div>
</div>
<div class="col-lg-6">
    <div class="form-group">
        <label class="col-form-label">Employee</label>
        <select class="select" name="add_policy_employee[]" multiple="multiple" id="add_policy_employee">
            @php
                $dpt_id = old('add_policy_department', isset($policy) ? $policy->department_id : '');
                if ($dpt_id != 'all' && $dpt_id != '') {
                    $employee_list = \App\Models\User::Role(\App\Enums\RolesEnum::Employee)
                        ->whereHas('employee_details', function ($query) use ($dpt_id) {
                            $query->where('department_id', $dpt_id);
                        })
                        ->with(['employee_details'])
                        ->get();
                }
            @endphp
            @if (isset($employee_list))
                @foreach ($employee_list as $employee)
                    <option value="{{ $employee->id }}" @if (in_array($employee->id, old('add_policy_employee', isset($policy) ? $policy->users->pluck('id')->toArray() : []))) selected @endif>
                        {{ $employee->first_name }} {{ $employee->last_name }}
                    </option>
                @endforeach
            @endif
        </select>

        <x-field-validation errorname="add_policy_employee" />
    </div>
</div>
