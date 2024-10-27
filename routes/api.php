<?php

use App\Http\Controllers\Api\AttendanceController;
use Carbon\Carbon;
use App\Models\ApiLog;
use App\Models\AssignLeave;
use App\Models\LeavePlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/mark/attendance', [AttendanceController::class, 'markAttendance']);
Route::get('/mark/attendance', [AttendanceController::class, 'doNotMarkAttendance']);// denies access
Route::get('/get/apilogs', [AttendanceController::class, 'getapilogs']);// list api logs
// recalculate logs from raw api data to device_logs
Route::get('/calculate/times', [AttendanceController::class, 'calculateApiTimes']);
Route::get('/process/reCountApilogs', [AttendanceController::class, 'reCountAttendance']);

Route::get('/process/apilogs', [AttendanceController::class, 'processApiLogs']);

// Reprocess logs from excel to device_logs
Route::get('/process/excel/{floor}', [AttendanceController::class, 'processExcel']);


Route::get('run-api', function () {
    // want date time in this format 20240202180936 using carbon
    $date = Carbon::now()->timezone('America/New_York')->format('YmdHis');
    return $date;
});


Route::get('/assign-leaves', function () {
    $employees = User::Role('employee')->get();
    if ($employees) {
        $leave_plans = LeavePlan::get();
        foreach ($leave_plans as $leave_plan) {
            foreach ($employees as $employee) {
                $assign_leave = AssignLeave::where('user_id', $employee->id)->where('leave_plan_id', $leave_plan->id)->first();
                if (!$assign_leave) {
                    AssignLeave::create([
                        'user_id' => $employee->id,
                        'leave_plan_id' => $leave_plan->id,
                        'remaining_leave' => $leave_plan->quantity,
                    ]);
                }
            }
        }
    }
});
