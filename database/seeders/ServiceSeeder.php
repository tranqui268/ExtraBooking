<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['id' => 'T001', 'service_name' => 'Thay nhớt','base_price' => 20, 'description' => 'Thay nhớt cho xe máy'],
            ['id' => 'T002', 'service_name' => 'Thay bugi','base_price' => 40, 'description' => 'Thay bugi cho xe máy'],
            ['id' => 'S001', 'service_name' => 'Sửa phanh','base_price' => 10, 'description' => 'Sửa phanh cho xe máy'],
            ['id' => 'B001', 'service_name' => 'Bảo dưỡng','base_price' => 120, 'description' => 'Thay nhớt cho xe máy'],
            ['id' => 'R001', 'service_name' => 'Rửa xe','base_price' => 5, 'description' => 'Rửa xe cho xe máy'],
        ];

        foreach ($services as $service) {
            DB::table('services')->insert([
                'id' => $service['id'],
                'service_name' => $service['service_name'],
                'base_price' => $service['base_price'],
                'description' => $service['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
