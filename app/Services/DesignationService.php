<?php

namespace App\Services;

use App\Models\Department;
use App\Models\Designation;

class DesignationService {
    public function get_designation_list(): array|object
    {
        return Designation::with([
            'department' => function ($query) {
                $query->withTrashed();
            }
        ])->get();
    }

    public function get_department_list(): array|object
    {
        return Department::all();
    }
    public function create(array $data)
    {
        Designation::create([
            'name' => $data['add_name'],
            'department_id' => $data['add_department']
        ]);
    }
    public function update(array $data, Designation $designation): bool
    {
        if ($designation) {
            $designation->update([
                'name' => $data['edit_name'],
                'department_id' => $data['edit_department']
            ]);
            return true;
        }
        return false;
    }
    public function destroy(string|int $id): bool
    {
        $designation = Designation::find($id);
        if ($designation) {
            $designation->delete();
            return true;
        }
        return false;
    }
}
