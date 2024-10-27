<?php

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Models\Department;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepartmentPolicy
{
    use HandlesAuthorization;
    /**
     * Create a new policy instance.
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo(PermissionEnum::ViewDepartment);
    }
    public function view(User $user, Department $department)
    {
        return $user->hasPermissionTo(PermissionEnum::ViewDepartment);
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo(PermissionEnum::AddDepartment);
    }

    public function update(User $user, Department $department)
    {
        return $user->hasPermissionTo(PermissionEnum::EditDepartment);
    }

    public function delete(User $user, Department $department)
    {
        return $user->hasPermissionTo(PermissionEnum::DeleteDepartment);
    }
}
