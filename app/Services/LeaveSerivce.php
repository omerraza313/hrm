<?php

namespace App\Services;

use App\Helpers\DateHelper;
use App\Helpers\LeaveHelper;
use App\Models\AssignLeave;
use App\Models\LeavePlan;
use App\Models\LeaveType;
use App\Models\Unit;
use App\Models\User;

class LeaveSerivce {
    public function get_months(): array
    {
        return DateHelper::getMonths();
    }

    public function get_leave_units(): array|object
    {
        $units = Unit::whereIn('name', ['days', 'hours'])->get();

        return $units;
    }

    public function get_leave_types(): array|object
    {
        $leave_types = LeaveType::all();

        return $leave_types;
    }

    public function store_leave_plan(array|object $data)
    {
        $fDate = LeaveHelper::get_month_number($data['leave_plan_from']);
        $tDate = LeaveHelper::get_month_number($data['leave_plan_to']);
        // leave_plan_document
        $leave = LeavePlan::create([
            'title' => $data['leave_plan_title'],
            'c_from_date' => $fDate,
            'c_to_date' => $tDate,
            'quantity' => $data['leave_plan_quantity'],
            'unit_id' => $data['leave_plan_unit'],
            'carry_forward' => $data['leave_plan_carry_f'],
            'consective_leaves' => $data['leave_plan_con_allow'],
            'apply_after_year' => $data['leave_plan_apply_year'],
            'apply_after_month' => $data['leave_plan_apply_month'],
            'leave_type_id' => $data['leave_plan_type'],
            'leave_gender_type' => $data['leave_plan_gender_type'],
        ]);

        $users = User::role('employee')->with('employee_details')->get();
        foreach ($users as $user) {
            if (($leave->leave_gender_type == $user->employee_details->gender) || $leave->leave_gender_type == 'both') {
                AssignLeave::create([
                    'leave_plan_id' => $leave->id,
                    'user_id' => $user->id,
                    'remaining_leave' => $leave->quantity,
                ]);
            }
        }
    }

    public function get_leave_plan_list()
    {
        return LeavePlan::with('unit', 'leave_type')->get();
    }

    public function delete_leave_plan(string|int $id)
    {
        $leave_plan = LeavePlan::find($id);
        $leave_plan->delete();
    }

    public function get_leave_plan(string|int $id)
    {
        return LeavePlan::find($id);
    }

    public function update_leave_plan(array|object $data, string|int $id)
    {
        $leave_plan = LeavePlan::find($id);
        
        if ($leave_plan) {
            $fDate = LeaveHelper::get_month_number($data['leave_plan_from']);
            $tDate = LeaveHelper::get_month_number($data['leave_plan_to']);
            
            $leave_plan->update([
                'title' => $data['leave_plan_title'],
                'c_from_date' => $fDate,
                'c_to_date' => $tDate,
                'quantity' => $data['leave_plan_quantity'],
                'unit_id' => $data['leave_plan_unit'],
                'carry_forward' => $data['leave_plan_carry_f'],
                'consective_leaves' => $data['leave_plan_con_allow'],
                'apply_after_year' => $data['leave_plan_apply_year'],
                'apply_after_month' => $data['leave_plan_apply_month'],
                'leave_type_id' => $data['leave_plan_type'],
                'leave_gender_type' => $data['leave_plan_gender_type'],
            ]);
        }
    }
}