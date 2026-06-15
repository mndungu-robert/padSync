<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // 1. First Admin Account
        User::create([
            'name' => 'System Admin One',
            'email' => 'admin1@padsync.com',
            'username' => 'admin1',
            'password' => Hash::make('password123'), // Securely hashes the password
            'role' => 'Admin',
            'status' => 'Approved',
        ]);

        // 2. Second Coordinator Account
        User::create([
            'name' => 'SCoordinator One',
            'email' => '2coordinator@padsync.com',
            'username' => 'co0rdinator',
            'password' => Hash::make('password123'),
            'role' => 'Coordinator',
            'status' => 'Pending',
        ]);
    }
}
