<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Shift 1: 10:00 - 18:00 WIB
        Shift::firstOrCreate(
            ['name' => 'Shift 1'],
            [
                'start_time' => '10:00:00',
                'end_time' => '18:00:00',
                'is_active' => true,
            ]
        );

        // Create Shift 2: 18:00 - 00:00 WIB (next day)
        Shift::firstOrCreate(
            ['name' => 'Shift 2'],
            [
                'start_time' => '18:00:00',
                'end_time' => '00:00:00',
                'is_active' => true,
            ]
        );
    }
}
