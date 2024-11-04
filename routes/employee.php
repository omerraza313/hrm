<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Employee\ProfileController;
use App\Http\Controllers\Employee\DashboardController;
use App\Http\Controllers\Employee\AttendenceController;
use App\Http\Controllers\Employee\EmployeeLeaveApplicationController;
use App\Http\Controllers\Employee\LeaveApplicationController;

Route::middleware(['web', 'role:employee'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Leave Application Routes
    Route::get('/leave-application', [LeaveApplicationController::class, 'index'])->name('leave.application.view');
    Route::get('/get_leave_plans', [LeaveApplicationController::class, 'get_leave_plans'])->name('leave.plans.values');
    Route::post('/apply_leave', [LeaveApplicationController::class, 'apply_leave'])->name('leave.apply');
    Route::delete('/delete-apply-leave/{id}', [LeaveApplicationController::class, 'delete_leave'])->name('leave.delete');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.view');

    // Attendence Routes
    Route::get('/attendence', [AttendenceController::class, 'index'])->name('attendence.view');
    Route::post('/attendence/mark/arrival', [AttendenceController::class, 'mark_arrival_attendance'])->name('attendence.mark.arrival');
    Route::post('/attendence/mark/leave/{id?}', [AttendenceController::class, 'mark_leave_attendance'])->name('attendence.mark.leave');
    Route::get('fetch_device_log', [AttendenceController::class, 'fetch_device_log'])->name('fetch.device_log');

    // Manager
    Route::get('/attendence/manager_view', [AttendenceController::class, 'attendence_view'])->name('employee.manager.attendence.regular.view');
    Route::get('/attendence/late_view', [AttendenceController::class, 'late_commers'])->name('employee.manager_view');

    // export
    Route::get('/attendence/manager_export', [AttendenceController::class, 'export'])->name('employee.attendence.export');
    Route::post('/attendence/update', [AttendenceController::class, 'update'])->name('manager_update');

    // Other Employee Leaves
    Route::get('/other/leave-applications', [EmployeeLeaveApplicationController::class, 'index'])->name('other.leave.application.view');
    Route::post('/other/update_leave_status', [EmployeeLeaveApplicationController::class, 'update_leave_status'])->name('other.leave.application.update.status');
    Route::delete('/other//leave/application/delete/{id}', [EmployeeLeaveApplicationController::class, 'delete_leave_application'])->name('other.leave.application.delete');
});
