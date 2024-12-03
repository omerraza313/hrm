<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['super_admin', 'admin', 'employee', 'manager'];
        foreach ($roles as $role) {
            $role = Role::firstOrCreate([
                'name' => $role
            ],[]);
            if($role->name == 'super_admin') {
                $role->syncPermissions(Permission::all());
            }
        }
    }
}
