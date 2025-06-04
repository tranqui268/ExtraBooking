<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    
    public function run(): void
    {
        User::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);
        User::create([
            'email' => 'employee1@example.com',
            'password' => Hash::make('12345678'),
            'role' => 'employee',
        ]);
        User::create([
            'email' => 'employee2@example.com',
            'password' => Hash::make('12345678'),
            'role' => 'employee',
        ]);
        User::create([
            'email' => 'employee3@example.com',
            'password' => Hash::make('12345678'),
            'role' => 'employee',
        ]);
    }
}
