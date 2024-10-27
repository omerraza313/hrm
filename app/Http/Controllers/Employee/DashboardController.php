<?php

namespace App\Http\Controllers\Employee;

use App\Models\User;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Employee\DashboardService;

class DashboardController extends Controller {
    public function __construct(protected DashboardService $dashboardService)
    {
    }
    public function index()
    {
        $data = $this->dashboardService->get_dashboard_data();

        return view('employee.dashboard.home', $data);
    }
}