<?php

namespace
App\Traits\Permissions;

trait AssignPermissions
{
    public function assignAllPermissions()
    {
        $user = $this;
        $department_permission = ["add_department", "edit_department", "view_department", "delete_department"];

        $designation_permission = ["add_designation", "edit_designation", "view_designation", "delete_designation"];

        $employee_permission = ["add_employee", "edit_employee", "view_employee", "delete_employee"];

        $permissions = array_merge($department_permission, $designation_permission, $employee_permission);

        foreach ($permissions as $permission) {
            $user->givePermissionTo($permission);
        }
    }
}
