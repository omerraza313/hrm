<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Salary;
use App\Models\Address;
use App\Enums\RolesEnum;
use App\Models\Employee;
use App\Models\Department;
use App\Models\UserDetail;
use App\Models\Designation;
use App\Models\FamilyContact;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EmployeeSeed extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dep_id = Department::inRandomOrder()->first()->id;
        $emp = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'password' => Hash::make('John_123'),
        ]);

        $emp->assignRole(RolesEnum::Employee);

        UserDetail::create([
            'pseudo_name' => 'JohnDoe',
            'dob' => '03/11/1986',
            'join_date' => '03/11/2023',
            'phone' => '+92123456789',
            'martial_status' => 'Single',
            'cnic' => '12395687451023598',
            'gender' => 'Male',
            'department_id' => $dep_id,
            'designation_id' => Designation::where('department_id', $dep_id)->inRandomOrder()->first()->id,
            'user_id' => $emp->id,
            'salary' => 50000,
            'blood_group' => "B+",
            'manager_id' => 2
        ]);

        FamilyContact::create([
            'name' => 'James Doe',
            'number' => '+92457896132',
            'relation' => 'Brother',
            'user_id' => $emp->id,
            'ice_status' => 1,
            'dob' => '03-11-1984'
        ]);

        Address::create([
            'address' => 'Saleem dummy house, Nawaz moor, near Peer Bazar, Bagrian Green Town',
            'city' => 'San Francisco',
            'state' => 'Seattle',
            'zip' => '123',
            'country' => 'United States',
            'address_type_id' => 3,
            'user_id' => $emp->id,
        ]);

        Salary::create([
            'date' => '2023-11-03',
            'salary' => '50000',
            'user_id' => $emp->id,
        ]);


        // Employee 2

        $dep_id = Department::inRandomOrder()->first()->id;
        $emp = User::create([
            'first_name' => 'Anne',
            'last_name' => 'Doe',
            'email' => 'annedoe@example.com',
            'password' => Hash::make('Anne_123'),
        ]);

        $emp->assignRole(RolesEnum::Employee);

        UserDetail::create([
            'pseudo_name' => 'AnneDoe',
            'dob' => '03/11/1986',
            'join_date' => '03/11/2023',
            'phone' => '+92123456789',
            'martial_status' => 'Single',
            'cnic' => '12395687451023598',
            'gender' => 'Female',
            'department_id' => $dep_id,
            'designation_id' => Designation::where('department_id', $dep_id)->inRandomOrder()->first()->id,
            'user_id' => $emp->id,
            'salary' => 50000,
            'blood_group' => "B+",
            'manager_id' => 2
        ]);

        FamilyContact::create([
            'name' => 'James Doe',
            'number' => '+92457896132',
            'relation' => 'Brother',
            'user_id' => $emp->id,
            'ice_status' => 1,
            'dob' => '03-11-1984'
        ]);

        Address::create([
            'address' => 'Saleem dummy house, Nawaz moor, near Peer Bazar, Bagrian Green Town',
            'city' => 'San Francisco',
            'state' => 'Seattle',
            'zip' => '123',
            'country' => 'United States',
            'address_type_id' => 3,
            'user_id' => $emp->id,

        ]);

        Salary::create([
            'date' => '2023-11-03',
            'salary' => '50000',
            'user_id' => $emp->id,
        ]);

    }
}