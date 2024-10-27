<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Services\Employee\OtherLeaveApplicationService;
use Illuminate\Http\Request;

class EmployeeLeaveApplicationController extends Controller {
    public function __construct(protected OtherLeaveApplicationService $otherLeaveApplicationService)
    {
    }
    public function index(Request $request)
    {
        $data = $this->otherLeaveApplicationService->get_main_data($request->all());
        return view('employee.employee-applications.view', $data);
    }
    public function update_leave_status(Request $request)
    {
        $request->validate([
            'apply_leave_id' => 'required|exists:applyleaves,id',
            'status' => 'required',
            'status_note' => 'nullable'
        ]);
        $leave_status = $this->otherLeaveApplicationService->change_leave_status($request->all());
        if ($leave_status) {
            return redirect()->back()->with('success', 'Leave status updated successfully');
        }
        return redirect()->back()->with('error', 'Leave status updatation failed');
    }

    public function delete_leave_application($id)
    {
        $deleteStatus = $this->otherLeaveApplicationService->delete_leave($id);
        if ($deleteStatus) {
            return redirect()->back()->with('success', 'Leave Deleted Successfully');
        }
        return redirect()->back()->with('error', 'Some error occurred');
    }
}