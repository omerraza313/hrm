<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $department_permission = ["add_department", "edit_department", "view_department", "delete_department"];

        $designation_permission = ["add_designation", "edit_designation", "view_designation", "delete_designation"];

        $employee_permission = ["add_employee", "edit_employee", "view_employee", "delete_employee"];

        $permissions = array_merge($department_permission, $designation_permission, $employee_permission);

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission
            ]);
        }
    }
}
