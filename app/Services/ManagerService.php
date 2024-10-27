<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Salary;
use App\Models\Address;
use App\Enums\RolesEnum;
use App\Models\Department;
use App\Models\UserDetail;
use App\Models\Designation;
use App\Models\FamilyContact;
use App\Enums\AddressTypeEnum;
use Illuminate\Support\Facades\Hash;

class ManagerService {
    public function get_managers_data($data)
    {
        $employees = User::Role(RolesEnum::Manager->value)
            ->when((isset($data['id']) && $data['id']), function ($query) use ($data) {
                return $query->where('id', $data['id']);
            })
            ->when((isset($data['name']) && $data['name']), function ($query) use ($data) {
                return $query->where('first_name', 'like', '%' . $data['name'] . '%')
                    ->orWhere('last_name', 'like', '%' . $data['name'] . '%');
            })
            ->with([
                'emergency_contacts',
                'address',
                'employee_details',
                'employee_details.designation',
            ]);
        if (!isset($data['status'])) {
            $employees = $employees->get();
        } elseif ($data['status'] == 'active') {
            $employees = $employees->get();
        } else {
            $employees = $employees->onlyTrashed()->get();
        }
        $departments = Department::all();
        $designations = Designation::with([
            'department' =>
                function ($query) {
                    $query->withTrashed();
                }
        ])->get();

        return compact('employees', 'designations', 'departments');
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

        $employee->assignRole(RolesEnum::Manager);
        $employee->assignRole(RolesEnum::Employee);
        $designation = Designation::where('name', 'Manager')->withTrashed()->first();
        if (!$designation) {
            $designation = Designation::create([
                'name' => 'Manager',
                'department_id' => $data['add_department']
            ]);
        }

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
            'designation_id' => $designation->id,
            'user_id' => $employee->id,
            'salary' => $data['add_salary'],
            'blood_group' => $data['add_blood_group']
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

    public function update_employee(array|object $data, User $emp): bool
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
            $employee->update([
                'first_name' => $data['edit_first_name'],
                'last_name' => $data['edit_last_name'],
                'email' => $data['edit_email'],
                'password' => Hash::make($data['edit_password']),
            ]);

            $employee_details->update([
                'join_date' => $data['edit_joining_date'],
                'phone' => $data['edit_phone'],
                'department_id' => $data['edit_department'],
                'salary' => $data['edit_salary'],
                'blood_group' => $data['edit_blood_group'],
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
        // dd($data);
        $employee = User::find($id);
        $employee->note = $data['delete_note'];
        $employee->clear_status = $data['delete_clearance'];
        $employee->save();
        if ($employee) {
            $employee->delete();
            return true;
        }
        return false;
    }
}