<?php

namespace App\Services\Employee;

use App\Models\LeavePlan;
use App\Models\Applyleaves;
use App\Models\AssignLeave;
use App\Enums\ApprovedStatusEnum;
use Illuminate\Support\Facades\Auth;
use App\Events\Employee\ApplyLeaveEvent;
use App\Events\Admin\DeleteEmployeeEvent;

class OtherLeaveApplicationService {
    public function get_main_data($data)
    {
        $total = Applyleaves::
        whereHas('employee.employee_details', function ($query){
            $query->where('manager_id', Auth::user()->id);
        })->
        with([
            'employee',
            'employee.employee_details'
        ])->get();

        $pendings = Applyleaves::where('status', ApprovedStatusEnum::Pending->value)
        ->whereHas('employee.employee_details', function ($query){
            $query->where('manager_id', Auth::user()->id);
        })
        ->with([
            'employee',
            'employee.employee_details'
        ])->get();

        $approved = Applyleaves::where('status', ApprovedStatusEnum::Approved->value)
        ->whereHas('employee.employee_details', function ($query){
            $query->where('manager_id', Auth::user()->id);
        })->with([
            'employee',
            'employee.employee_details'
        ])->get();


        if (!isset($data['name'])) {
            $data['name'] = null;
        }
        if (!isset($data['types'])) {
            $data['types'] = null;
        }
        if (!isset($data['status'])) {
            $data['status'] = null;
        }
        if (!isset($data['from_date'])) {
            $data['from_date'] = null;
        }
        if (!isset($data['to_date'])) {
            $data['to_date'] = null;
        }


        $apply_leaves = Applyleaves::
            when($data['name'], function ($query, $name) {
                $query->whereHas('employee', function ($query) use ($name) {
                    $query->where('first_name', 'like', '%' . $name . '%')
                        ->orWhere('last_name', 'like', '%' . $name . '%');
                });
            })
            ->when($data['types'], function ($query, $types) {
                $query->whereHas('adjust_leaves', function ($query) use ($types) {
                    $query->where('leave_plan_id', $types);
                });
            })
            ->when($data['status'] != null, function ($query) use ($data) {
                $query->where('status', $data['status']);
            })
            ->when($data['from_date'], function ($query, $from_date) {
                $query->whereHas('adjust_leaves', function ($query) use ($from_date) {
                    $query->where('leave_date', '>=', $from_date);
                });
            })
            ->when($data['to_date'], function ($query, $to_date) {
                $query->whereHas('adjust_leaves', function ($query) use ($to_date) {
                    $query->where('leave_date', '<=', $to_date);
                });
            })
            ->whereHas('employee.employee_details', function ($query){
                $query->where('manager_id', Auth::user()->id);
            })
            ->with([
                'employee' => function ($query) {
                    $query->withTrashed();
                },
                'employee.employee_details.designation',
                'approved_by',
                'adjust_leaves',
                'adjust_leaves.leave_plan' => function ($query) {
                    $query->withTrashed();
                }
            ])
            ->get();
        // dd($apply_leaves->toArray());
        $leave_types = LeavePlan::all();
        return compact('total', 'pendings', 'approved', 'apply_leaves', 'leave_types');
    }


    public function change_leave_status(mixed $data): bool
    {
        if ($data['status'] == ApprovedStatusEnum::Approved->value) {
            $this->approved_status($data);
        } else {
            $this->declined_status($data);
        }
        return true;
    }

    private function approved_status(mixed $data)
    {
        $apply_leave = Applyleaves::where('id', $data['apply_leave_id'])->with('adjust_leaves', 'employee', 'adjust_leaves.leave_plan')->first();
        if ($apply_leave->status == ApprovedStatusEnum::Declined->value) {
            foreach ($apply_leave->adjust_leaves as $adjust_leave) {
                $assignLeaves = AssignLeave::where('user_id', $apply_leave->user_id)->where('leave_plan_id', $adjust_leave->leave_plan_id)->first();
                $assignLeaves->remaining_leave = $assignLeaves->remaining_leave - $adjust_leave->quantity;
                $assignLeaves->save();
            }
        }
        $apply_leave->note = null;
        $apply_leave->status = ApprovedStatusEnum::Approved->value;
        $apply_leave->approved_by = Auth::user()->id;
        $apply_leave->save();
        event(new ApplyLeaveEvent($apply_leave->employee, $apply_leave, Auth::user()));
    }

    private function declined_status(mixed $data)
    {
        $apply_leave = Applyleaves::where('id', $data['apply_leave_id'])->with('adjust_leaves', 'employee', 'adjust_leaves.leave_plan')->first();
        foreach ($apply_leave->adjust_leaves as $adjust_leave) {
            $assignLeaves = AssignLeave::where('user_id', $apply_leave->user_id)->where('leave_plan_id', $adjust_leave->leave_plan_id)->first();
            $assignLeaves->remaining_leave = $assignLeaves->remaining_leave + $adjust_leave->quantity;
            $assignLeaves->save();
        }
        $apply_leave->note = $data['status_note'];
        $apply_leave->status = ApprovedStatusEnum::Declined->value;
        $apply_leave->approved_by = Auth::user()->id;
        $apply_leave->save();
        event(new ApplyLeaveEvent($apply_leave->employee, $apply_leave, Auth::user()));
    }

    public function delete_leave($id)
    {
        $applyLeave = Applyleaves::where('id', $id)->with('adjust_leaves')->first();
        if ($applyLeave) {
            foreach ($applyLeave->adjust_leaves as $adjust_leave) {
                $assignLeave = AssignLeave::where('user_id', $applyLeave->user_id)->where('leave_plan_id', $adjust_leave->leave_plan_id)->first();
                $assignLeave->remaining_leave = $assignLeave->remaining_leave + $adjust_leave->quantity;
                $assignLeave->save();
            }
            $applyLeave->delete();
            try {
                event(new DeleteEmployeeEvent($applyLeave->user_id, $applyLeave->id));
            } catch (\Throwable $th) {
                //throw $th;
            }
            return true;
        } else {
            return false;
        }
    }
}
