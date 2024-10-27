<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Attendence;
use App\Models\Department;
use App\Models\Designation;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class DashboardController extends Controller {

    public function __construct(protected DashboardService $dashboardService)
    {
    }
    public function index()
    {
        $data = $this->dashboardService->get_dashboard_data();
        // dd($data);
        return view('admin.dashboard.dashboard', $data);
    }
}