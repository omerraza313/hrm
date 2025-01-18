<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Salary;
use App\Models\Address;
use App\Enums\RolesEnum;
use App\Models\Department;
use App\Models\UserDetail;
use App\Models\Designation;
use App\Models\FamilyContact;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ManagerSeed extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dep_id = Department::inRandomOrder()->first()->id;
        $designation = Designation::updateOrCreate([
            'name' => 'Manager',
            'department_id' => $dep_id
        ],[]);
        $emp = User::updateOrCreate(['email' => 'layrain@example.com'],[
            'first_name' => 'Lay',
            'last_name' => 'Rain',
            
            'password' => Hash::make('Admin_123'),
        ]);

        $emp->assignRole(RolesEnum::Manager);
        $emp->assignRole(RolesEnum::Employee);

        UserDetail::updateOrCreate(['user_id' => $emp->id],[
            'pseudo_name' => 'layrain',
            'dob' => '03/11/1986',
            'join_date' => '03/11/2023',
            'phone' => '+92123456789',
            'martial_status' => 'Single',
            'cnic' => '12395687451023598',
            'gender' => 'Male',
            'department_id' => $dep_id,
            'designation_id' => $designation->id,
            'salary' => 50000
        ]);

        FamilyContact::updateOrCreate(['user_id' => $emp->id],[
            'name' => 'James Doe',
            'number' => '+92457896132',
            'relation' => 'Brother',
            
            'ice_status' => 1,
            'dob' => '03-11-1984'
        ]);

        Address::updateOrCreate(['user_id' => $emp->id],[
            'address' => 'Saleem dummy house, Nawaz moor, near Peer Bazar, Bagrian Green Town',
            'city' => 'San Francisco',
            'state' => 'Seattle',
            'zip' => '123',
            'country' => 'United States',
            'address_type_id' => 3,
            
        ]);

        Salary::updateOrCreate(['user_id' => $emp->id],[
            'date' => '2023-11-03',
            'salary' => '50000',
            
        ]);
    }
}