<!-- Search Filter -->
<form>
    <div class="row filter-row">
        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
            <div class="form-group form-focus">
                <input type="text" class="form-control floating" name="name" value="{{ request()->name ?? '' }}">
                <label class="focus-label">Employee Name</label>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
            <div class="form-group form-focus select-focus">
                <select class="select floating" name="designation">
                    <option value=""> -- Select -- </option>
                    @foreach ($designations as $designation)
                        <option value="{{ $designation->id }}" @if (request()->designation == $designation->id) selected @endif>
                            {{ $designation->name }}</option> )
                    @endforeach
                </select>
                <label class="focus-label">Designation</label>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
            <div class="form-group form-focus select-focus">
                <select class="select floating" name="month">
                    <option value=""> -- Select -- </option>
                    @foreach (\App\Helpers\DateHelper::getMonths() as $index => $month)
                        <option value="0{{ $index + 1 }}" @if (request()->month == '0' . ($index + 1)) selected @endif>
                            {{ $month }}</option>
                    @endforeach
                </select>
                <label class="focus-label">Month</label>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
            <div class="form-group form-focus select-focus">
                <select class="select floating" name="year">
                    <option value=""> -- Select -- </option>
                    @php
                        $year = 1990;
                    @endphp
                    @while ($year <= 2050)
                        <option value="{{ $year }}" @if (request()->year == $year) selected @endif>
                            {{ $year }}</option>
                        @php
                            $year++;
                        @endphp
                    @endwhile
                </select>
                <label class="focus-label">Year</label>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-2 col-12">
            <button type="submit" class="btn btn-success w-100"> Search </button>
        </div>
    </div>
</form>
<!-- /Search Filter -->
