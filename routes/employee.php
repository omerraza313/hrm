<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Employee\ProfileController;
use App\Http\Controllers\Employee\DashboardController;
use App\Http\Controllers\Employee\AttendenceController;
use App\Http\Controllers\Employee\EmployeeLeaveApplicationController;
use App\Http\Controllers\Employee\LeaveApplicationController;

Route::middleware(['web'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Leave Application Routes
    Route::get('/leave-application', [LeaveApplicationController::class, 'index'])->name('leave.application.view')->middleware('can:leaves.index');
    Route::get('/get_leave_plans', [LeaveApplicationController::class, 'get_leave_plans'])->name('leave.plans.values');
    Route::post('/apply_leave', [LeaveApplicationController::class, 'apply_leave'])->name('leave.apply')->middleware('can:leave.apply');
    Route::delete('/delete-apply-leave/{id}', [LeaveApplicationController::class, 'delete_leave'])->name('leave.delete')->middleware('can:leave.delete');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.view');

    // Attendence Routes
    Route::get('/attendence', [AttendenceController::class, 'index'])->name('attendence.view')->middleware('can:attendances.index');
    Route::post('/attendence/mark/arrival', [AttendenceController::class, 'mark_arrival_attendance'])->name('attendence.mark.arrival')->middleware('can:attendances.index');
    Route::post('/attendence/mark/leave/{id?}', [AttendenceController::class, 'mark_leave_attendance'])->name('attendence.mark.leave')->middleware('can:attendances.index');

    Route::get('fetch_device_log', [AttendenceController::class, 'fetch_device_log'])->name('fetch.device_log');

    // Manager
    Route::get('/attendence/manager_view', [AttendenceController::class, 'attendence_view'])->name('employee.manager.attendence.regular.view')->middleware('can:attendances.index');
    Route::get('/attendence/late_view', [AttendenceController::class, 'late_commers'])->name('employee.manager_view')->middleware('can:attendances.index');

    // export
    Route::get('/attendence/manager_export', [AttendenceController::class, 'export'])->name('employee.attendence.export');
    Route::post('/attendence/update', [AttendenceController::class, 'update'])->name('manager_update');

    // Other Employee Leaves
    Route::get('/other/leave-applications', [EmployeeLeaveApplicationController::class, 'index'])->name('other.leave.application.view')->middleware('can:leaves.index');
    Route::post('/other/update_leave_status', [EmployeeLeaveApplicationController::class, 'update_leave_status'])->name('other.leave.application.update.status')->middleware('can:leave.edit');
    Route::delete('/other//leave/application/delete/{id}', [EmployeeLeaveApplicationController::class, 'delete_leave_application'])->name('other.leave.application.delete')->middleware('can:leave.delete');
});
