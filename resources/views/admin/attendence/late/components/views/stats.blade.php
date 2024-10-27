<!-- Search Filter -->
@php
    $emp_id = $_GET['employee_id'] ?? '';
    $to_date = $_GET['to_date'] ?? '';
    $from_date = $_GET['from_date'] ?? '';
@endphp
<form action="">
    <div class="row filter-row align-items-center">
        <div class="col-sm-6 col-md-3">
            <div class="form-group">
                <label class="focus-label">Employee Name</label>
                <select name="employee_id" class="select">
                    <option value="">--Select--</option>
                    @if (isset($employees))
                        @foreach ($employees as $employee)

                            <option value="{{ $employee->id }}"
                                @if ($emp_id == $employee->id) @selected(true) @endif>
                                {{ $employee->first_name }} {{ $employee->last_name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="form-group">
                <label class="focus-label">From Date</label>
                <input type="text" name="from_date" class="form-control floating datetimepicker" id=""
                    value="{{ $from_date }}">
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="form-group">
                <label class="focus-label">To Date</label>
                <input type="text" name="to_date" class="form-control floating datetimepicker" id=""
                    value="{{ $to_date }}">
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="d-grid">
                <button type="submit" class="btn btn-success"> Search </button>
            </div>
        </div>
    </div>
</form>
<!-- /Search Filter -->
