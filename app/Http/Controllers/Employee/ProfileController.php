<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Services\Employee\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct( protected ProfileService $profileService ){
    }
    public function index(){
        $employee = $this->profileService->get_employee();
        $departments = $this->profileService->get_department_list();
        return view('employee.profile.view', ['employee' => $employee, 'departments' => $departments]);
    }
}
