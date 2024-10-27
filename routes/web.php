<?php

use App\Http\Controllers\SuperAdmin\ManagerController;
use App\Http\Controllers\SuperAdmin\PayrollController;
use App\Services\RouteService;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SuperAdmin\AttendenceController;
use App\Http\Controllers\SuperAdmin\LeaveController as SuperAdminLeaveController;
use App\Http\Controllers\SuperAdmin\ProfileController as SuperAdminProfileController;
use App\Http\Controllers\SuperAdmin\EmployeeController as SuperAdminEmployeeController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\DepartmentController as SuperAdminDepartmentController;
use App\Http\Controllers\SuperAdmin\DesignationController as SuperAdminDesignationController;
use App\Http\Controllers\SuperAdmin\EmployeeBankController as SuperAdminEmployeeBankController;
use App\Http\Controllers\SuperAdmin\PersonalInfoController as SuperAdminPersonalInfoController;
use App\Http\Controllers\SuperAdmin\FamilyContactController as SuperAdminFamilyContactController;
use App\Http\Controllers\SuperAdmin\EmerygenceyContactController as SuperAdminEmerygenceyContactController;
use App\Http\Controllers\SuperAdmin\LateComerController;
use App\Http\Controllers\SuperAdmin\LeaveApplicationController;
use App\Http\Controllers\SuperAdmin\PolicyController;
use App\Http\Controllers\SuperAdmin\PolicySwapController;
use App\Http\Controllers\TestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'loginPost'])->name('login');
    Route::get('/forget-password', [AuthController::class, 'forget_password_view'])->name('forget.password');
    Route::post('/forget-password', [AuthController::class, 'forget_password'])->name('forget.password');
    Route::get('/reset-password', [AuthController::class, 'reset_password_view'])->name('reset.password');
    Route::post('/reset-password', [AuthController::class, 'reset_password'])->name('reset.password');
});
Route::group(['middleware' => 'auth'], function () {
    Route::get('/', function () {
        return RouteService::get_view_with_role();
    });

    Route::get('/home', function () {
        return RouteService::get_view_with_role();
    });
    Route::delete('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/check-db', function () { return DB::connection()->getDatabaseName(); });

    Route::middleware(['web', 'role:super_admin|admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('admin.dashboard');

        // Department Routes
        Route::resource('/department', SuperAdminDepartmentController::class)->names(RouteService::get_resource_names('admin.department'));
        Route::post('/get-departments', [SuperAdminDepartmentController::class, 'get_departments'])->name('admin.department.get.department');
        Route::get('get-designation/{department_id}', [SuperAdminDepartmentController::class, 'get_designation'])->name('admin.department.get.designation');

        // Designation Routes
        Route::resource('/designation', SuperAdminDesignationController::class)->names(RouteService::get_resource_names('admin.designation'));

        // Employee Routes
        Route::resource('/employee', SuperAdminEmployeeController::class)->names(RouteService::get_resource_names('admin.employee'));
        Route::post('restore/employee/{id}', [SuperAdminEmployeeController::class, 'restore_employee'])->name('admin.employee.restore');
        Route::get('/get/employee', [SuperAdminEmployeeController::class, 'get_employee'])->name('admin.employee.get.employee');
        Route::post('/get/employee/department', [SuperAdminEmployeeController::class, 'get_employee_department'])->name('admin.employee.get.employee.department');

        Route::resource('/manager', ManagerController::class)->names(RouteService::get_resource_names('admin.manager'));
        // Employee bank
        Route::post('/store-employee-bank', [SuperAdminEmployeeBankController::class, 'store_bank'])->name('admin.employee.bank.store');

        // Employee Experience and Education and profile Veiw
        Route::get('/profile/{id}', [SuperAdminProfileController::class, 'index'])->name('profile.view');
        route::post('/store-education', [SuperAdminProfileController::class, 'store_update_education'])->name('profile.store_education');
        route::post('/store-experience', [SuperAdminProfileController::class, 'store_update_experience'])->name('profile.store_experience');

        // Employee Family, Emerygency, Personal and Profile Info
        Route::resource('/family-contact', SuperAdminFamilyContactController::class)->names(RouteService::get_resource_names('familycontact'));
        Route::post('/emergency-contact', [SuperAdminEmerygenceyContactController::class, 'store_update'])->name('emergency.contact');
        Route::post('/peronal-info', [SuperAdminPersonalInfoController::class, 'store_update'])->name('personal.info');
        Route::post('profile-info', [SuperAdminProfileController::class, 'store_update_profile_info'])->name('profile.info');

        // Employee Leave Routes
        Route::get('/leave-settings', [SuperAdminLeaveController::class, 'leave_view'])->name('admin.leave.view');
        Route::post('store-leave-plan', [SuperAdminLeaveController::class, 'store_leave_plan'])->name('admin.leave.plan');
        Route::delete('/delete-leave-plan/{id}', [SuperAdminLeaveController::class, 'delete_leave_plan'])->name('admin.leave.plan.delete');
        Route::get('/edit-leave-plan/{id}', [SuperAdminLeaveController::class, 'edit_leave_plan'])->name('admin.leave.plan.edit');


        // Leave Application Routes
        Route::get('/leave-applications', [LeaveApplicationController::class, 'index'])->name('admin.leave.application.view');
        Route::post('update_leave_status', [LeaveApplicationController::class, 'update_leave_status'])->name('admin.leave.application.update.status');
        Route::get('/get_leave_plans', [LeaveApplicationController::class, 'get_leave_plans'])->name('admin.leave.application.get');
        Route::post('/apply-leave-employee', [LeaveApplicationController::class, 'apply_leave_employee'])->name('admin.apply.leave.employee');
        Route::delete('/leave/application/delete/{id}', [LeaveApplicationController::class, 'delete_leave_application'])->name('admin.delete.leave.application');

        // Policey Routes
        Route::get('/policy-view', [PolicyController::class, 'index'])->name('admin.policy.view');
        Route::post('/store-policy', [PolicyController::class, 'store'])->name('admin.policy.store');
        Route::delete('/delete/policy/{id}', [PolicyController::class, 'delete'])->name('admin.policy.delete');
        Route::post('/assign-policy', [PolicyController::class, 'assign'])->name('admin.policy.assign');


        // Attendence
        Route::get('/attendence/view', [AttendenceController::class, 'index'])->name('admin.attendence.view');
        Route::post('/attendence/update', [AttendenceController::class, 'update'])->name('admin.attendence.update');
        Route::get('/attendance/export', [AttendenceController::class, 'export'])->name('admin.attendence.export');
        Route::get('/attendance/adminexport', [AttendenceController::class, 'adminexport'])->name('admin.attendence.adminexport');


        // Late Comers Routes
        Route::get('/attendence/late/view', [LateComerController::class, 'index'])->name('admin.attendence.late.view');

        // policy Swap Routes
        Route::post('/policy/swap/job', [PolicySwapController::class, 'store'])->name('admin.policy.swap.job.store');


        // Payrolls
        Route::get('/payroll/employee', [PayrollController::class, 'index'])->name('admin.payroll.employee');
        Route::get('/payroll/employee/view', [PayrollController::class, 'salary_view'])->name('admin.payroll.employee.view');
    });

    include __DIR__ . '/employee.php';
});

Route::get('/test', [TestController::class, 'index']);
Route::get('/test/auto_checkout', [TestController::class, 'auto_checkout']);
