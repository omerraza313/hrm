<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Employee;
use App\Enums\PermissionEnum;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeePolicy
{
    use HandlesAuthorization;
    /**
     * Create a new policy instance.
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo(PermissionEnum::ViewEmployee);
    }
    public function view(User $user, Employee $employee)
    {
        return $user->hasPermissionTo(PermissionEnum::ViewEmployee);
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo(PermissionEnum::AddEmployee);
    }

    public function update(User $user, Employee $employee)
    {
        return $user->hasPermissionTo(PermissionEnum::EditEmployee);
    }

    public function delete(User $user, Employee $employee)
    {
        return $user->hasPermissionTo(PermissionEnum::DeleteEmployee);
    }
}
