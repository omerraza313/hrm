<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionArr = [
            'manage_employees' => [
                'employees.index',
                'employee.show',
                'employees.edit',
                'employees.delete',
            ],
            'manage_attendances' => [
                'attendances.index',
                'attendance.show',
                'attendance.edit',
                'attendance.delete',
                'attendance.export',
                'attendance.check-in',
                'attendance.check-out',
            ],
            'manage_departments' => [
                'departments.index',
                'department.show',
                'department.edit',
                'department.delete',
            ],
            'manage_designations' => [
                'designations.index',
                'designation.show',
                'designation.edit',
                'designation.delete',
            ],
            'manage_leaves' => [
                'leaves.index',
                'leave.show',
                'leave.edit',
                'leave.delete',
            ],
            'manage_leaves' => [
                'leaves.index',
                'leave.show',
                'leave.edit',
                'leave.delete',
                'leave.apply',
                'leave.approve',
                'leave.reject',
            ],
            'manage_policies' => [
                'policies.index',
                'policy.show',
                'policy.edit',
                'policy.delete',
            ],
            'manage_policies' => [
                'policies.index',
                'policy.show',
                'policy.edit',
                'policy.delete',
                'policy.assign',
            ],
            'manage_roles' => [
                'roles.index',
                'role.show',
                'role.edit',
                'role.delete',
                'role.assign-permissions',
            ],
        ];

        foreach($permissionArr as $group => $permissions) {
            foreach($permissions as $permission) {
                Permission::updateOrCreate(['name' => $permission], ['group' => $group]);
            }
        }
        
        // remove this code snippet once removed from server
        $removePermissionArr = [
            'add_department',
            'edit_department',
            'view_department',
            'delete_department',
            'add_designation',
            'edit_designation',
            'view_designation',
            'delete_designation',
            'add_employee',
            'edit_employee',
            'view_employee',
            'delete_employee',
        ];

        foreach ($removePermissionArr as $permissionName) {
            $permission = Permission::findByName($permissionName);

            $roles = Role::whereHas('permissions', function($query) use ($permission) {
                $query->where('id', $permission->id);
            })->get();

            foreach ($roles as $role) {
                $role->revokePermissionTo($permission);
            }

            $users = User::whereHas('permissions', function($query) use ($permission) {
                $query->where('id', $permission->id);
            })->get();

            foreach ($users as $user) {
                $user->revokePermissionTo($permission);
            }
        }

        Permission::whereIn('name', $removePermissionArr)->delete();

    }
}
