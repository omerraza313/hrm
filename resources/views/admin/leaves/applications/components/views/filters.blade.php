<!-- Search Filter -->
@php
    $name = $_GET['name'] ?? null;
    $types = $_GET['types'] ?? null;
    $status = $_GET['status'] ?? null;
    $from_date = $_GET['from_date'] ?? null;
    $to_date = $_GET['to_date'] ?? null;
@endphp
<form action="">
    <div class="row filter-row">
        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
            <div class="form-group form-focus">
                <input type="text" class="form-control floating" name="name" value="{{ $name }}">
                <label class="focus-label">Employee Name</label>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
            <div class="form-group form-focus select-focus">
                <select class="select floating" name="types">
                    <option value=""> -- Select -- </option>
                    @if (!empty($leave_types))

                        @foreach ($leave_types as $leave_type)
                            <option value="{{ $leave_type->id }}" {{ $types == $leave_type->id ? 'selected' : '' }}>
                                {{ $leave_type->title }} </option>
                        @endforeach

                    @endif
                </select>
                <label class="focus-label">Leave Type</label>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
            <div class="form-group form-focus select-focus">
                <select class="select floating" name="status">
                    <option value=""> -- Select -- </option>
                    <option value="0" {{ $status == '0' ? 'selected' : '' }}> Pending </option>
                    <option value="2" {{ $status == '2' ? 'selected' : '' }}> Approved </option>
                    <option value="1" {{ $status == '1' ? 'selected' : '' }}> Declined </option>
                </select>
                <label class="focus-label">Leave Status</label>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
            <div class="form-group form-focus">
                <div class="cal-icon">
                    <input class="form-control floating datetimepicker" type="text" name="from_date"
                        value="{{ $from_date }}">
                </div>
                <label class="focus-label">From</label>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
            <div class="form-group form-focus">
                <div class="cal-icon">
                    <input class="form-control floating datetimepicker" type="text" name="to_date"
                        value="{{ $to_date }}">
                </div>
                <label class="focus-label">To</label>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
            <button type="submit" class="btn btn-success w-100"> Search </button>
        </div>
    </div>
</form>
<!-- /Search Filter -->
