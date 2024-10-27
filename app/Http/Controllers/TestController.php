<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Salary;
use App\Enums\RolesEnum;
use App\Models\Attendence;
use Illuminate\Http\Request;
use App\Enums\AttendenceEnum;
use App\Helpers\PolicyHelper;
use App\Schedule\AttendanceSchedule;
use App\Schedule\AutoCheckoutAttendance;
use Exception;

class TestController extends Controller {
    public function index()
    {

    }

    public function auto_checkout()
    {
        AutoCheckoutAttendance::auto_checkout();
        // AttendanceSchedule::attendance_marking();
    }
}