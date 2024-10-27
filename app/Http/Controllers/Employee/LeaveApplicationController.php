<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequests\LeaveApplication\StoreApplyLeaveRequest;
use App\Services\Employee\LeaveApplicationService;
use Illuminate\Http\Request;

class LeaveApplicationController extends Controller {
    public function __construct(protected LeaveApplicationService $leaveApplicationService)
    {
    }
    public function index()
    {
        $data = $this->leaveApplicationService->get_leave_application_view_data();
        return view('employee.application.leave.view', $data);
    }

    public function get_leave_plans()
    {
        $leave_plans = $this->leaveApplicationService->get_leave_plans();

        return response()->json(['data' => $leave_plans], 200);
    }

    public function apply_leave(StoreApplyLeaveRequest $request)
    {
        $data = $request->validated();
        $store_status = $this->leaveApplicationService->store_leave($data);
        if ($store_status) {
            return redirect()->back()->with('success', 'Leave Applied Successfully');
        }
        return redirect()->back()->with('error', 'Reaming leave is not enough or Cant apply consective leaves');
    }

    public function delete_leave($id)
    {
        $deleteStatus = $this->leaveApplicationService->delete_leave($id);
        if ($deleteStatus) {
            return redirect()->back()->with('success', 'Leave Deleted Successfully');
        }
        return redirect()->back()->with('error', 'Some error occurred');
    }
}