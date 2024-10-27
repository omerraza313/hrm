<?php

namespace Database\Seeders;

use App\Enums\UserGenderEnum;
use App\Models\AssignLeave;
use App\Models\LeavePlan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeavePlanSeed extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LeavePlan::create([
            'title' => 'Annual leaves',
            'c_from_date' => '1',
            'c_to_date' => '12',
            'quantity' => '30',
            'unit_id' => 1,
            'carry_forward' => '3',
            'consective_leaves' => '3',
            'apply_after_year' => '0',
            'apply_after_month' => '3',
            'leave_type_id' => 1,
            'leave_gender_type' => 'both'
        ]);

        LeavePlan::create([
            'title' => 'Unpaid Leaves',
            'c_from_date' => '1',
            'c_to_date' => '12',
            'quantity' => '30',
            'unit_id' => 1,
            'carry_forward' => '3',
            'consective_leaves' => '3',
            'apply_after_year' => '0',
            'apply_after_month' => '3',
            'leave_type_id' => 2,
            'leave_gender_type' => 'both'
        ]);

        LeavePlan::create([
            'title' => 'Female Leave',
            'c_from_date' => '1',
            'c_to_date' => '12',
            'quantity' => '30',
            'unit_id' => 1,
            'carry_forward' => '3',
            'consective_leaves' => '3',
            'apply_after_year' => '0',
            'apply_after_month' => '3',
            'leave_type_id' => 1,
            'leave_gender_type' => UserGenderEnum::female
        ]);

        LeavePlan::create([
            'title' => 'Male Leaves',
            'c_from_date' => '1',
            'c_to_date' => '12',
            'quantity' => '30',
            'unit_id' => 1,
            'carry_forward' => '3',
            'consective_leaves' => '3',
            'apply_after_year' => '0',
            'apply_after_month' => '3',
            'leave_type_id' => 1,
            'leave_gender_type' => UserGenderEnum::male
        ]);


        LeavePlan::create([
            'title' => 'Men Leaves',
            'c_from_date' => '1',
            'c_to_date' => '12',
            'quantity' => '48',
            'unit_id' => 2,
            'carry_forward' => '3',
            'consective_leaves' => '4',
            'apply_after_year' => '0',
            'apply_after_month' => '3',
            'leave_type_id' => 1,
            'leave_gender_type' => UserGenderEnum::male
        ]);

        $users = User::role('employee')->with('employee_details')->get();
        foreach ($users as $user) {
            $leave_plans = LeavePlan::whereIn('leave_gender_type', [$user->employee_details->gender, 'both'])->get();
            foreach ($leave_plans as $leave) {
                AssignLeave::create([
                    'leave_plan_id' => $leave->id,
                    'user_id' => $user->id,
                    'remaining_leave' => $leave->quantity,
                ]);
            }
        }
    }
}