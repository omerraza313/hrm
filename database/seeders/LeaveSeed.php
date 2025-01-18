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
        LeaveType::updateOrCreate(['leave_type' => 'paid'],[]);
        LeaveType::updateOrCreate(['leave_type' => 'unpaid'],[]);
        Unit::updateOrCreate(['name' => 'days'],[]);
        Unit::updateOrCreate(['name' => 'hours'],[]);
    }
}
