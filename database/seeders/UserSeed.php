<?php

namespace Database\Seeders;

use App\Enums\RolesEnum;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeed extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['super_admin', 'admin', 'employee', 'manager'];
        foreach ($roles as $role) {
            Role::create([
                'name' => $role
            ]);
        }

        $user = User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('Admin_123')
        ]);

        $user->assignRole(RolesEnum::SuperAdmin);

        $user->assignAllPermissions();
    }
}