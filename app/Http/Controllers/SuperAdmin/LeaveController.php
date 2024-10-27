<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Leave\StoreLeavePlanRequest;
use App\Services\LeaveSerivce;
use Illuminate\Http\Request;
use App\Models\LeavePlan;

class LeaveController extends Controller
{
    public function __construct(protected LeaveSerivce $leaveSerivce)
    {
        // $this->authorizeResource(Employee::class, 'employee');
    }
    public function leave_view()
    {
        $months = $this->leaveSerivce->get_months();
        $units = $this->leaveSerivce->get_leave_units();
        $leave_types = $this->leaveSerivce->get_leave_types();
        $leave_plans = $this->leaveSerivce->get_leave_plan_list();
        $data_ary = compact('months', 'units', 'leave_types', 'leave_plans');
        return view('admin.leaves.view', $data_ary);
    }

    public function store_leave_plan(StoreLeavePlanRequest $request)
    {
        $data = $request->validated();
        $this->leaveSerivce->store_leave_plan($data);

        return redirect()->back()->with('success', 'Leave plan save successfully');
    }

    public function delete_leave_plan($id) {
        $this->leaveSerivce->delete_leave_plan($id);

        return redirect()->back()->with('success','Leave plan delete successfully');
    }

    public function edit_leave_plan($id) {

        $leavePlan = LeavePlan::find($id);
        $months = $this->leaveService->get_months();
        $units = $this->leaveService->get_leave_units();
        $leave_types = $this->leaveService->get_leave_types();
        dd($leavePlan);
        return view('admin.leaves.components.modals.define-leave-edit-modal', compact('leavePlan', 'months', 'units', 'leave_types'));
    }

    public function update_leave_plan(StoreLeavePlanRequest $request, $id) {
        $data = $request->validated();
        $this->leaveService->update_leave_plan($data, $id);

        return redirect()->back()->with('success', 'Leave plan updated successfully');
    }
}
