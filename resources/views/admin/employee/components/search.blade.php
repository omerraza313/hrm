@php
    $des = $_GET['designation'] ?? '';
    $id = $_GET['id'] ?? '';
    $name = $_GET['name'] ?? '';
    $status = $_GET['status'] ?? '';
@endphp
<form action="">
    <div class="row filter-row">
        <div class="col-sm-6 col-md-3">
            <div class="form-group form-focus">
                <input type="text" class="form-control floating" name="id" value="{{ $id }}">
                <label class="focus-label">Employee ID</label>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="form-group form-focus">
                <input type="text" class="form-control floating" name="name" value="{{ $name }}">
                <label class="focus-label">Employee Name</label>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="form-group form-focus select-focus">
                <select class="select floating" name="designation">
                    <option value="">--select--</option>
                    @if (isset($designations))
                        @foreach ($designations as $designation)
                            <option value="{{ $designation->id }}" @if ($designation->id == $des) selected @endif>
                                {{ $designation->name }}</option>
                        @endforeach
                    @endif
                </select>
                <label class="focus-label">Designation</label>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="form-group form-focus select-focus">
                <select class="select floating" name="status">
                    <option value="">--select--</option>
                    <option value="active" @if ('active' == $status) selected @endif>Active</option>
                    <option value="deactive" @if ('deactive' == $status) selected @endif>Deactive</option>
                </select>
                <label class="focus-label">Status</label>
            </div>
        </div>
        <div class="col-sm-6 col-md-12">
            <div class="text-end">
                <button type="submit" class="btn btn-success w-25"> Search </button>
            </div>
        </div>
    </div>
</form>
