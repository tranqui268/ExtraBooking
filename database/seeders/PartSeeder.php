<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PartSeeder extends Seeder
{
 
    public function run(): void
    {
        DB::table('parts')->insert([
            [
                'part_code' => 'PT001',
                'name' => 'Lọc dầu động cơ',
                'brand' => 'Toyota',
                'description' => 'Phụ tùng lọc dầu chính hãng Toyota',
                'unit_price' => 150000.00,
                'stock_quantity' => 100,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'part_code' => 'PT002',
                'name' => 'Má phanh trước',
                'brand' => 'Honda',
                'description' => 'Bộ má phanh trước dành cho dòng xe tay ga Honda',
                'unit_price' => 250000.00,
                'stock_quantity' => 50,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'part_code' => 'PT003',
                'name' => 'Bugi NGK',
                'brand' => 'NGK',
                'description' => 'Bugi đánh lửa cao cấp NGK',
                'unit_price' => 90000.00,
                'stock_quantity' => 200,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'part_code' => 'PT004',
                'name' => 'Lốp xe máy Dunlop',
                'brand' => 'Dunlop',
                'description' => 'Lốp xe chất lượng cao cho xe số',
                'unit_price' => 350000.00,
                'stock_quantity' => 35,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'part_code' => 'PT005',
                'name' => 'Nhông xích DID',
                'brand' => 'DID',
                'description' => 'Bộ nhông xích DID dành cho xe thể thao',
                'unit_price' => 420000.00,
                'stock_quantity' => 20,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
