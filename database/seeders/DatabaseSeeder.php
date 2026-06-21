<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SchoolSeeder::class,
            UserSeeder::class,
            EnrollmentSeeder::class,
            InventorySeeder::class,
            DonorSeeder::class,
            DonationSeeder::class,
            DistributionSeeder::class,
            ReceiptConfirmationSeeder::class,
            ShortfallReportSeeder::class,
            AuditLogSeeder::class,
        ]);
    }
}
