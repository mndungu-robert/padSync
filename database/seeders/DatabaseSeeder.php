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
        $this->call([
            UserSeeder::class,
            SchoolSeeder::class,
            EnrollmentSeeder::class,
            InventorySeeder::class,
            DonorSeeder::class,
            DonationSeeder::class,
            DistributionSeeder::class,
            ShortfallReportSeeder::class,
            AuditLogSeeder::class,
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
