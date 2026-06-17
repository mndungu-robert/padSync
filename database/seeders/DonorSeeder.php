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
        Donor::create([
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
            'pad_count' => 100,
            'donor_type' => 'Individual',
            'organization_name' => null,
        ]);
        Donor::create([
            'name' => 'Rotary Club Nairobi',
            'email' => 'rotary@example.com',
            'pad_count' => 500,
            'donor_type' => 'Organization',
            'organization_name' => 'Rotary Club',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
