<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeaveSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LeaveType::create(['leave_type' => 'paid']);
        LeaveType::create(['leave_type' => 'unpaid']);
        Unit::create(['name' => 'days']);
        Unit::create(['name' => 'hours']);
    }
}
