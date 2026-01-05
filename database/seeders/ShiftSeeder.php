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
        // Create or update Shift 1: 06:00 - 18:00 WIB
        // Use updateOrCreate so existing installs get updated to the new start time
        Shift::updateOrCreate(
            ['name' => 'Shift 1'],
            [
                'start_time' => '06:00:00',
                'end_time' => '18:00:00',
                'is_active' => true,
            ]
        );

        // Create or update Shift 2: 18:00 - 00:00 WIB (next day)
        Shift::updateOrCreate(
            ['name' => 'Shift 2'],
            [
                'start_time' => '18:00:00',
                'end_time' => '00:00:00',
                'is_active' => true,
            ]
        );
    }
}
