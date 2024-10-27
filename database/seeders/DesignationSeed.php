<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Designation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DesignationSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $designations = ['React Js Developer', 'Laravel Developer', 'Php Developer'];
        foreach ($designations as $designation) {
            Designation::create([
                'name' => $designation,
                'department_id' => Department::inRandomOrder()->first()->id
            ]);
        }
    }
}
