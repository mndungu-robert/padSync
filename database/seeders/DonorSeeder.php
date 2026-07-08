<?php

namespace Database\Seeders;

use App\Models\Donor;
use Illuminate\Database\Seeder;

class DonorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Donor::updateOrCreate(
            ['email' => 'jane.doe@example.com'],
            [
                'name' => 'Jane Doe',
                'pad_count' => 100,
                'donor_type' => 'Individual',
                'organization_name' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        Donor::updateOrCreate(
            ['email' => 'rotary@example.com'],
            [
                'name' => 'Rotary Club Nairobi',
                'pad_count' => 500,
                'donor_type' => 'Organization',
                'organization_name' => 'Rotary Club',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        Donor::updateOrCreate(
            ['email' => 'safi.foundation@example.com'],
            [
                'name' => 'Safi Foundation',
                'pad_count' => 350,
                'donor_type' => 'Organization',
                'organization_name' => 'Safi Foundation',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
