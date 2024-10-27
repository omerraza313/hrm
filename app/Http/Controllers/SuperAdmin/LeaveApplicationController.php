<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Leave\ApplyLeaveApplicationRequest;
use App\Services\LeaveApplicationService;
use Illuminate\Http\Request;

class LeaveApplicationController extends Controller {
    public function __construct(protected LeaveApplicationService $leaveApplicationService)
    {

    }
    public function index(Request $request)
    {
        $total = $this->leaveApplicationService->get_total_leaves();
        $pendings = $this->leaveApplicationService->get_pending_leaves();
        $approved = $this->leaveApplicationService->get_approved_leaves();
        $declined = $this->leaveApplicationService->get_declined_leaves();

        $leaves = $this->leaveApplicationService->get_leave_types();
        $apply_leaves = $this->leaveApplicationService->get_apply_leaves($request->all());
        $employees = $this->leaveApplicationService->get_employees();
//        dd([$leaves, $total, $pendings]);
        $data = compact('apply_leaves', 'pendings', 'employees', 'leaves', 'total', 'approved', 'declined');
        return view('admin.leaves.applications.view', $data);
    }

    public function update_leave_status(Request $request)
    {
        $request->validate([
            'apply_leave_id' => 'required|exists:applyleaves,id',
            'status' => 'required',
            'status_note' => 'nullable'
        ]);
        $leave_status = $this->leaveApplicationService->change_leave_status($request->all());
        if ($leave_status) {
            return redirect()->back()->with('success', 'Leave status updated successfully');
        }
    }

    public function get_leave_plans(Request $request)
    {
        $employee_id = $request->employee_id;
        $get_leave_plans = $this->leaveApplicationService->get_leave_plans($employee_id);

        return response()->json(['leave_plans' => $get_leave_plans], 200);
    }

    public function apply_leave_employee(ApplyLeaveApplicationRequest $request)
    {
        $data = $request->validated();
//        $data = $request;
        $storeStatus = $this->leaveApplicationService->apply_employee_leave($data);
        if ($storeStatus) {
            return redirect()->back()->with('success', 'Leave Applied For Employee successfully');
        }
        dd($storeStatus);
        return redirect()->back()->with('error', 'Some Error Occured!');
    }

    public function delete_leave_application($id)
    {
        $deleteStatus = $this->leaveApplicationService->delete_leave($id);
        if ($deleteStatus) {
            return redirect()->back()->with('success', 'Leave Deleted Successfully');
        }
        return redirect()->back()->with('error', 'Some error occurred');
    }
}
