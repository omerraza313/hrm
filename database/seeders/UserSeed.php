<?php

namespace Database\Seeders;

use App\Enums\RolesEnum;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeed extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate([
            'email' => 'admin@gmail.com',
        ],[
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'password' => Hash::make('Admin_123')
        ]);

        $user->assignRole(RolesEnum::SuperAdmin);

    }
}