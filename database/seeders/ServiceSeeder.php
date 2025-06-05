<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['id' => 'T001', 'service_name' => 'Thay nhớt','duration' => 30,'base_price' => 120000,'maintenance_interval' => 2, 'description' => 'Thay nhớt cho xe máy'],
            ['id' => 'T002', 'service_name' => 'Thay bugi','duration' => 20,'base_price' => 100000,'maintenance_interval' => 4, 'description' => 'Thay bugi cho xe máy'],
            ['id' => 'S001', 'service_name' => 'Sửa phanh','duration' => 45,'base_price' => 80000,'maintenance_interval' => 4, 'description' => 'Sửa phanh cho xe máy'],
            ['id' => 'B001', 'service_name' => 'Bảo dưỡng','duration' => 60,'base_price' => 200000,'maintenance_interval' => 6, 'description' => 'Thay nhớt cho xe máy'],
            ['id' => 'R001', 'service_name' => 'Rửa xe','duration' => 15,'base_price' => 50000,'maintenance_interval' => 1, 'description' => 'Rửa xe cho xe máy'],
        ];

        foreach ($services as $service) {
            DB::table('services')->insert([
                'id' => $service['id'],
                'service_name' => $service['service_name'],
                'duration' => $service['duration'],
                'base_price' => $service['base_price'],
                'maintenance_interval' => $service['maintenance_interval'],
                'description' => $service['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
