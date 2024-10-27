<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\PermissionEnum;
use App\Models\Designation;

class DesignationPolicy
{
    /**
     * Create a new policy instance.
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo(PermissionEnum::ViewDesignation);
    }
    public function view(User $user, Designation $designation)
    {
        return $user->hasPermissionTo(PermissionEnum::ViewDesignation);
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo(PermissionEnum::AddDesignation);
    }

    public function update(User $user, Designation $designation)
    {
        return $user->hasPermissionTo(PermissionEnum::EditDesignation);
    }

    public function delete(User $user, Designation $designation)
    {
        return $user->hasPermissionTo(PermissionEnum::DeleteDesignation);
    }
}
