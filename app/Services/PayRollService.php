<?php

namespace App\Services;

use App\Enums\RolesEnum;
use App\Models\Department;
use App\Models\Designation;
use App\Models\User;
use Carbon\Carbon;

class PayRollService {
    public function get_payroll_details($data): array
    {
        $employees = User::Role(RolesEnum::Employee->value)
            ->when(isset ($data['designation']), function ($query) use ($data) {
                $query->whereHas('employee_details', function ($query) use ($data) {
                    $query->where('designation_id', $data['designation']);
                });
            })
            ->when(isset ($data['name']), function ($query) use ($data) {
                $query->where('first_name', 'LIKE', '%' . $data['name'] . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $data['name'] . '%');
            })
            ->with([
                'employee_details.designation' => function ($query) {
                    $query->withTrashed();
                },
                'employee_details.department' => function ($query) {
                    $query->withTrashed();
                },
                'attendences' => function ($query) use ($data) {
                    $query->when(isset ($data['month']) && isset ($data['year']), function ($query) use ($data) {
                        $query->whereMonth('arrival_date', $data['month'])->whereYear('arrival_date', $data['year'])->where('status', '!=', '2');
                    });
                }
            ])->get();
        $designations = Designation::all();
        return compact('employees', 'designations');
    }

    public function get_employee_salary_details($data): array
    {
        $employee = User::Role(RolesEnum::Employee->value)
            ->with([
                'employee_details.designation',
                'employee_details.department',
                'salary' => function ($query) use ($data) {
                    $date = Carbon::createFromFormat('Y-m-d', $data['date']);
                    $query->whereMonth('date', $date->format('m'))->whereYear('date', $date->format('Y'));
                }
            ])
            ->findOrFail($data['id']);
        return compact('employee');
    }
}