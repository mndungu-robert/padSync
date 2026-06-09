<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // 1. First Admin Account
        User::create([
            'name' => 'System Admin One',
            'email' => 'admin1@padsync.com',
            'username' => 'admin1',
            'password' => Hash::make('password123'), // Securely hashes the password
            'role' => 'Admin',
            'status' => 'Approved',
        ]);

        // 2. Second Admin Account
        User::create([
            'name' => 'System Admin Two',
            'email' => 'admin2@padsync.com',
            'username' => 'admin2',
            'password' => Hash::make('password123'),
            'role' => 'Admin',
            'status' => 'Approved',
        ]);
    }
}
