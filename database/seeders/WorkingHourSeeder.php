<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkingHourSeeder extends Seeder
{
    
    public function run(): void
    {
        $workingHours = [
            ['day_of_week' => 1, 'start_time' => '08:00:00', 'end_time' => '20:00:00'],
            ['day_of_week' => 2, 'start_time' => '08:00:00', 'end_time' => '20:00:00'],
            ['day_of_week' => 3, 'start_time' => '08:00:00', 'end_time' => '20:00:00'],
            ['day_of_week' => 4, 'start_time' => '08:00:00', 'end_time' => '20:00:00'],
            ['day_of_week' => 5, 'start_time' => '08:00:00', 'end_time' => '20:00:00'],
            ['day_of_week' => 6, 'start_time' => '08:00:00', 'end_time' => '12:00:00'],
        ];

        foreach ($workingHours as $value) {
            DB::table('working_hours')->insert([
                'day_of_week' => $value['day_of_week'],
                'start_time' => $value['start_time'],
                'end_time' => $value['end_time'],                
            ]);
        }
    }
}
