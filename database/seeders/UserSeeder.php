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

        User::updateOrCreate(
            ['email' => 'admin1@padsync.com'],
            [
                'name' => 'System Admin One',
                'username' => 'admin1',
                'password' => Hash::make('password123'),
                'role' => 'Admin',
                'status' => 'Approved',
                'school_id' => null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'pm1@padsync.com'],
            [
                'name' => 'Program Manager One',
                'username' => 'pm1',
                'password' => Hash::make('password123'),
                'role' => 'Program Manager',
                'status' => 'Approved',
                'school_id' => null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'coordinator1@padsync.com'],
            [
                'name' => 'Coordinator One',
                'username' => 'coordinator1',
                'password' => Hash::make('password123'),
                'role' => 'Coordinator',
                'status' => 'Approved',
                'school_id' => $schoolOneId,
            ]
        );

        User::updateOrCreate(
            ['email' => 'coordinator2@padsync.com'],
            [
                'name' => 'Coordinator Two',
                'username' => 'coordinator2',
                'password' => Hash::make('password123'),
                'role' => 'Coordinator',
                'status' => 'Pending',
                'school_id' => $schoolTwoId,
            ]
        );
    }
}
