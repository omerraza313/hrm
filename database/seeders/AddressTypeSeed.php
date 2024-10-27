<?php

namespace Database\Seeders;

use App\Models\AddressType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressTypeSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $type = ['permanent', 'current', 'both'];
        foreach ($type as $value) {
            AddressType::create([
                'name' => $value
            ]);
        };
    }
}
