<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
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
        $schoolIds = DB::table('schools')->orderBy('school_id')->pluck('school_id');
        $schoolOneId = $schoolIds->get(0);
        $schoolTwoId = $schoolIds->get(1) ?? $schoolOneId;

        User::create([
            'name' => 'System Admin One',
            'email' => 'admin1@padsync.com',
            'username' => 'admin1',
            'password' => Hash::make('password123'),
            'role' => 'Admin',
            'status' => 'Approved',
            'school_id' => null,
        ]);

        User::create([
            'name' => 'Program Manager One',
            'email' => 'pm1@padsync.com',
            'username' => 'pm1',
            'password' => Hash::make('password123'),
            'role' => 'Program Manager',
            'status' => 'Approved',
            'school_id' => null,
        ]);

        User::create([
            'name' => 'Coordinator One',
            'email' => 'coordinator1@padsync.com',
            'username' => 'coordinator1',
            'password' => Hash::make('password123'),
            'role' => 'Coordinator',
            'status' => 'Approved',
            'school_id' => $schoolOneId,
        ]);

        User::create([
            'name' => 'Coordinator Two',
            'email' => 'coordinator2@padsync.com',
            'username' => 'coordinator2',
            'password' => Hash::make('password123'),
            'role' => 'Coordinator',
            'status' => 'Pending',
            'school_id' => $schoolTwoId,
        ]);
    }
}
