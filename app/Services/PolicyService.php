<?php

namespace App\Services;

use App\Enums\RolesEnum;
use App\Models\Department;
use App\Models\PolicyPayRollSetting;
use App\Models\Policy;
use App\Models\PolicyHolidayOvertime;
use App\Models\PolicyOvertime;
use App\Models\PolicyWorkingDay;
use App\Models\PolicyWorkingSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PolicyService {
    public function get_department_list(): array|object
    {
        $department_list = Department::get();

        return $department_list;
    }

    public function get_enployee_list(): array|object
    {
        $employee_list = User::role(RolesEnum::Employee->value)->get();

        return $employee_list;
    }

    public function store($data): bool
    {
        try {
            DB::beginTransaction();
            if (!isset($data['add_policy_employee'])) {
                $data['add_policy_employee'] = [];
            }
            $policy = Policy::create([
                'policy' => $data['add_policy_name']
            ]);

            if (!$policy) {
                throw new \Exception('Error creating policy');
            }

            $this->attach_department($policy->id, [$data['add_policy_department']]);

            $this->attach_employees($policy->id, [$data['add_policy_department']], $data['add_policy_employee']);

            PolicyPayRollSetting::create([
                'policy_id' => $policy->id,
                'generation_type' => $data['add_policy_payslip_gen_type'],
                'off_days_per_month' => $data['add_policy_off_days'],
                'working_hours' => $data['add_policy_working_hours'],
                // 'minutes' => $data['add_policy_minutes'],
                'max_shift_retaining_hours' => $data['add_policy_shift_hours']
            ]);

            PolicyWorkingSetting::create([
                'policy_id' => $policy->id,
                'shift_start' => $data['add_policy_shift_start'],
                'shift_close' => $data['add_policy_shift_close'],
                'late_c_l_t' => $data['add_policy_late_c_l_t'],
                'early_arrival_policy' => $data['add_policy_e_a_p'],
                'force_timeout' => $data['add_policy_force_timeout'],
                'timeout_policy' => $data['add_policy_timeout_policy'],
                'late_minute_monthly_bucket' => $data['add_policy_monthly_late_minute'],
                'late_comers_penalty' => $data['add_policy_late_comers_penalty'],
            ]);

            $working_days = json_decode($data['add_policy_working_array']);
            foreach ($working_days as $working_day) {
                PolicyWorkingDay::create([
                    'policy_id' => $policy->id,
                    'day' => $working_day->id,
                    'start_time' => $working_day->start_time,
                    'close_time' => $working_day->end_time,
                    'active' => $working_day->active,
                ]);
            }

            PolicyOvertime::create([
                'policy_id' => $policy->id,
                'ot_status' => $data['add_policy_overtime_status'],
                'ot_start' => $data['add_policy_ot_atfer_closing_duty'],
                'ot_min_minutes' => $data['add_policy_ot_min_minutes'],
                'ot_rate_status' => $data['add_policy_ot_rate'],
                'ot_rate' => $data['add_policy_ot_rate_value'],
                'ot_amount' => $data['add_policy_ot_amount'],
            ]);

            PolicyHolidayOvertime::create([
                'policy_id' => $policy->id,
                'holiday_ot_status' => $data['add_policy_holiday_ot'],
                'holiday_ot_rate' => $data['add_policy_holiday_ot_rate'],
                'holiday_ot_amount' => $data['add_policy_holiday_ot_amount'],
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            // dd($e);
            return false;
        }

        return true;
    }

    private function attach_department($policy_id, array $departments = [], $sync = false)
    {
        $policy = Policy::with([
            'departments' =>
                function ($query) {
                    $query->withTrashed();
                }
        ])->where('id', $policy_id)->first();
        $department_array = [];
        foreach ($departments as $department) {
            if ($department == 'all') {
                $department_array = Department::all()->pluck('id')->toArray();
                break;
            } else {
                $department_array[] = $department;
            }
        }


        // foreach ($department_array as $dept) {
        //     // dd($dept);
        //     $oldPolicies = Policy::where('id', '!=', $policy_id)->with([
        //         'departments' => function ($query) {
        //             $query->withTrashed();

        //         }
        //     ])->get();
        //     foreach ($oldPolicies as $key => $oldPolicy) {
        //         $status = 0; // Replace with your desired status value

        //         // Iterate through each department and update the status column in the pivot table
        //         $oldPolicy->departments->each(function ($department) use ($status, $dept) {
        //             if ($department->id == $dept) {
        //                 $department->pivot->update(['status' => $status]);
        //             }

        //         });
        //     }
        // }
        if ($sync == true) {
            $policy->departments()->detach();
        }
        $policy->departments()->attach($department_array, [
            'start_time' => Carbon::now(),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
    private function attach_employees($policy_id, array $departments = [], array $employees = [], $sync = false)
    {
        $policy = Policy::with([
            'departments' => function ($query) {
                $query->withTrashed();
            },
            'users'
        ])->where('id', $policy_id)->first();
        if (!$employees) {
            foreach ($departments as $department) {
                if ($department == 'all') {
                    $department_array = Department::all()->pluck('id')->toArray();
                    break;
                } else {
                    $department_array[] = $department;
                }
            }
            foreach ($department_array as $dept) {
                $employee_list = User::Role(RolesEnum::Employee->value)->whereHas(
                    'employee_details',
                    function ($query) use ($dept) {
                        $query->where('department_id', $dept);
                    }
                )->get()->pluck('id')->toArray();
            }
        } else {
            $employee_list = $employees;
        }
        foreach ($employee_list as $key => $employee) {
            $oldPolicies = Policy::where('id', '!=', $policy_id)->with('users')->get();

            foreach ($oldPolicies as $oldPolicy) {
                $status = 0; // Replace with your desired status value

                // Iterate through each department and update the status column in the pivot table
                $oldPolicy->users->each(function ($users) use ($status, $employee) {
                    if ($users->id == $employee) {
                        $users->pivot->update(['status' => $status]);
                    }
                });
            }

            // dd($oldPolicies->toArray());
        }

        if ($sync == true) {
            $policy->users()->detach();
        }

        $policy->users()->attach($employee_list, [
            'start_time' => Carbon::now(),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function destroy(string|int $id): bool
    {
        $policy = Policy::find($id);
        if ($policy) {
            $policy->delete();
            return true;
        }
        return false;
    }

    public function assign($data): bool
    {
        $policy = Policy::find($data['policy_id']);
        if ($policy) {
            $this->attach_department($data['policy_id'], $data['department_ids'], true);
            $this->attach_employees($data['policy_id'], [], $data['employee_ids'], true);
            return true;
        }
        return false;
    }
}