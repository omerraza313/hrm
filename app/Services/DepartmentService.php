<?php

namespace App\Services;

use App\Models\Department;

class DepartmentService {
    public function get_department_list(): array|object
    {
        return Department::all();
    }
    public function create(array $data)
    {
        Department::create([
            'name' => $data['add_name']
        ]);
    }
    public function update(array $data, Department $department): bool
    {
        if ($department) {
            $department->update([
                'name' => $data['edit_name']
            ]);
            return true;
        }
        return false;
    }
    public function destroy(string|int $id): bool
    {
        $department = Department::find($id);
        if ($department) {
            $department->delete();
            return true;
        }
        return false;
    }

    public function get_designation(string|int $dep_id): array|object
    {
        $designations = Department::where('id', $dep_id)->with([
            'designations' => function ($query) {
                $query->get();
            }
        ])->first();
        return $designations;
    }
}
