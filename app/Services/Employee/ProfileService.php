<?php

namespace App\Services\Employee;

use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;

class ProfileService
{
    public function get_employee() : object|array {
        $employee = User::where('id', Auth::user()->id)->with([
            'emergency_contacts',
            'family_contacts',
            'address',
            'employee_details',
            'employee_details.designation',
            'employee_details.department',
            'bank',
            'educations',
            'experiences'
        ])->first();

        return $employee;
    }

    public function get_department_list() : object|array {
        return Department::all();
    }
}
