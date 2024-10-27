<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\PayRollService;
use Illuminate\Http\Request;

class PayrollController extends Controller {
    public function __construct(protected PayRollService $payRollService)
    {
    }
    public function index(Request $request)
    {
        $data = $this->payRollService->get_payroll_details($request->all());
        return view('admin.payroll.index', $data);
    }
    public function salary_view(Request $request)
    {
        $data = $this->payRollService->get_employee_salary_details($request->all());
        return view('admin.payroll.salary', $data);
    }
}