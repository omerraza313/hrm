<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Salary;
use App\Models\Address;
use App\Models\DateLog;
use App\Enums\RolesEnum;
use App\Models\Employee;
use App\Models\LeavePlan;
use App\Models\Department;
use App\Models\UserDetail;
use App\Helpers\DateHelper;
use App\Models\AssignLeave;
use App\Models\Designation;
use App\Models\DeactiveUser;
use App\Models\FamilyContact;
use App\Enums\AddressTypeEnum;
use Illuminate\Support\Facades\Hash;

class EmployeeService {
    public function get_department_list()
    {
        return Department::all();
    }

    public function get_designation_list()
    {
        return Designation::with([
            'department' =>
                function ($query) {
                    $query->withTrashed();
                }
        ])->get();
    }
    public function create(array|object $data): void
    {
        // dd($data['add_image']->extension());
        $image = time() . '.' . $data['add_image']->extension();
        $data['add_image']->move('images/employee/', $image);
        $employee = User::create([
            'first_name' => $data['add_first_name'],
            'last_name' => $data['add_last_name'],
            'email' => $data['add_email'],
            'password' => Hash::make($data['add_password']),
            'image' => $image
        ]);
        if ($employee->hasRole(RolesEnum::Manager->value) && $data['add_role'] = 1) {
            $employee->removeRole(RolesEnum::Manager->value);
        }
        if ($data['add_role'] == '2' || $data['add_role'] == 2) {
            $data['add_report_manager'] = null;
            $employee->assignRole(RolesEnum::Manager);
        }
        $employee->assignRole(RolesEnum::Employee);
        // dd($data);
        UserDetail::create([
            'pseudo_name' => $data['add_pseudo_name'],
            'dob' => $data['add_dob'],
            'join_date' => $data['add_join_date'],
            'phone' => $data['add_phone'],
            'martial_status' => $data['add_martial_status'],
            'cnic' => $data['add_cnic'],
            'gender' => $data['add_gender'],
            'department_id' => $data['add_department'],
            'designation_id' => $data['add_designation'],
            'user_id' => $employee->id,
            'salary' => $data['add_salary'],
            'blood_group' => $data['add_blood_group'],
            'manager_id' => $data['add_report_manager'],
        ]);

        DateLog::create([
            'user_id' => $employee->id,
            'date' => $data['add_join_date'],
            'dateable_type' => User::class,
            'dateable_id' => $employee->id,
            'type' => 'join'
        ]);

        $salaryDate = $data['add_join_date'];
        $carbonDate = Carbon::createFromFormat('d/m/Y', $salaryDate);

        // Format the Carbon instance into the desired format
        $formattedDate = $carbonDate->format('Y-m-d');

        Salary::create([
            'date' => $formattedDate,
            'salary' => $data['add_salary'],
            'user_id' => $employee->id,
        ]);

        FamilyContact::create([
            'name' => $data['add_em_contact_name'],
            'number' => $data['add_em_contact_num'],
            'relation' => $data['add_em_contact_relation'],
            'ice_status' => 1,
            'user_id' => $employee->id,
        ]);
        if ($data['add_curr_status'] == 'false' || $data['add_curr_status'] == false) {
            $this->create_permanent_address($data, $employee->id);
            $this->create_current_address($data, $employee->id);
        } else {
            $this->create_both_address($data, $employee->id);
        }

        $this->assign_leaves($employee);
    }

    private function assign_leaves($employee)
    {
        $leave_plans = LeavePlan::get();
        if ($leave_plans) {
            foreach ($leave_plans as $leave_plan) {
                if (($leave_plan->leave_gender_type == $employee->employee_details->gender) || $employee->leave_gender_type == 'both') {
                    AssignLeave::create([
                        'leave_plan_id' => $leave_plan->id,
                        'user_id' => $employee->id,
                        'remaining_leave' => $leave_plan->quantity,
                    ]);
                }
            }
        }
    }
    public function create_permanent_address(array|object $data, string|int $employee_id)
    {
        Address::create([
            'address' => $data['add_per_address'],
            'city' => $data['add_per_city'],
            'state' => $data['add_per_state'],
            'zip' => $data['add_per_zip'],
            'country' => $data['add_per_country'],
            'address_type_id' => AddressTypeEnum::permanent,
            'user_id' => $employee_id,
        ]);
    }
    public function create_current_address(array|object $data, string|int $employee_id)
    {
        Address::create([
            'address' => $data['add_curr_address'],
            'city' => $data['add_curr_city'],
            'state' => $data['add_curr_state'],
            'zip' => $data['add_curr_zip'],
            'country' => $data['add_curr_country'],
            'address_type_id' => AddressTypeEnum::current,
            'user_id' => $employee_id,
        ]);
    }
    public function create_both_address(array|object $data, string|int $employee_id)
    {
        Address::create([
            'address' => $data['add_per_address'],
            'city' => $data['add_per_city'],
            'state' => $data['add_per_state'],
            'zip' => $data['add_per_zip'],
            'country' => $data['add_per_country'],
            'address_type_id' => AddressTypeEnum::both,
            'user_id' => $employee_id,
        ]);
    }

    public function get_employees($data = [])
    {
        if (!isset($data['id'])) {
            $data['id'] = null;
        }
        if (!isset($data['name'])) {
            $data['name'] = null;
        }
        if (!isset($data['designation'])) {
            $data['designation'] = null;
        }
        $user = User::role('employee')
            // ->whereDoesntHave('roles', function ($query) {
            //     $query->where('name', 'manager');
            // })
            ->when($data['id'], function ($query, $id) {
                return $query->where('id', $id);
            })
            ->when($data['name'], function ($query, $name) {
                return $query->where('first_name', 'like', '%' . $name . '%')
                    ->orWhere('last_name', 'like', '%' . $name . '%');
            })
            ->when($data['designation'], function ($query, $designation) {
                return $query->whereHas('employee_details', function ($query) use ($designation) {
                    return $query->where('designation_id', $designation);
                });
            })
            ->with([
                'emergency_contacts',
                'address',
                'employee_details',
                'employee_details.manager' => function ($query) {
                    $query->withTrashed();
                },
                'employee_details.designation' => function ($query) {
                    $query->withTrashed();
                },
                'deactive_user'
            ])
        ->orderBy('id', 'asc');
        if (!isset($data['status'])) {
            $user = $user->get();
        } elseif ($data['status'] == 'active') {
            $user = $user->get();
        } else {
            $user = $user->onlyTrashed()->get();
        }
        return $user;
    }

    public function get_manager_list()
    {
        $managers = User::Role(RolesEnum::Manager->value)->get();

        return $managers;
    }
    public function update_employee(array|object $data, Employee $emp): bool
    {
        $employee = User::find($emp->id);
        if (isset($data['edit_image']) && $data['edit_image']) {
            $image = time() . '.' . $data['edit_image']->extension();
            $data['edit_image']->move('images/employee/', $image);

            $employee->update([
                'image' => $image
            ]);
        }
        $employee_details = UserDetail::where('user_id', $employee->id)->first();

        if ($employee && $employee_details) {
            if ($data['edit_role'] == 2) {
                $data['edit_report_manager'] = null;
                if (!$employee->hasRole(RolesEnum::Manager->value)) {
                    $employee->assignRole(RolesEnum::Manager->value);
                }
            } else {
                if ($employee->hasRole(RolesEnum::Manager->value)) {
                    $employee->removeRole(RolesEnum::Manager->value);
                }
            }
            $employee->update([
                'first_name' => $data['edit_first_name'],
                'last_name' => $data['edit_last_name'],
                'email' => $data['edit_email'],
                'password' => Hash::make($data['edit_password']),
            ]);


            if ($employee_details->join_date != DateHelper::datedashformat($data['edit_joining_date'])) {
                DateLog::create([
                    'user_id' => $employee->id,
                    'date' => $data['edit_joining_date'],
                    'dateable_type' => User::class,
                    'dateable_id' => $employee->id,
                    'type' => 'join'
                ]);
            }

            $employee_details->update([
                'join_date' => $data['edit_joining_date'],
                'phone' => $data['edit_phone'],
                'department_id' => $data['edit_department'],
                'designation_id' => $data['edit_designation'],
                'salary' => $data['edit_salary'],
                'blood_group' => $data['edit_blood_group'],
                'manager_id' => $data['edit_report_manager'],
            ]);

            Salary::create([
                'date' => Carbon::now()->format('Y-m-d'),
                'salary' => $data['edit_salary'],
                'user_id' => $employee->id,
            ]);
            return true;
        }
        return false;
    }

    public function destroy($data, string|int $id): bool
    {
        $employee = User::find($id);
        if ($employee) {
            DeactiveUser::updateOrCreate(
                ['user_id' => $employee->id],
                [
                    'user_id' => $employee->id,
                    'notice_period_served' => $data['delete_notice_period_served'],
                    'notice_period_date' => $data['delete_notice_period_date'],
                    'notice_period_duration' => $data['delete_notice_period_duration'],
                    'exit_date' => $data['delete_exit_date'],
                    'all_cleared' => $data['delete_clearance'],
                    'reason' => $data['delete_reason'],
                    'comments' => $data['delete_note'],
                ]
            );
            $employee->delete();
            return true;
        }
        return false;
    }
}
