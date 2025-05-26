<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use function Laravel\Prompts\table;

class EmployeeSeeder extends Seeder
{   
    public function run(): void
    {
        $employees = [
            ['id' => 'MM001', 'name' => 'Nguyễn Văn A', 'experience' => 3, 'rating' => 4,5],
            ['id' => 'MM002', 'name' => 'Nguyễn Văn B', 'experience' => 1, 'rating' => 3],
            ['id' => 'MM003', 'name' => 'Nguyễn Văn C', 'experience' => 7, 'rating' => 5],
        ];

        foreach ($employees as $employee){
            DB::table('employees')->insert([
                'id' => $employee['id'],
                'name' => $employee['name'],
                'experience' => $employee['experience'],
                'rating' => $employee['rating'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
