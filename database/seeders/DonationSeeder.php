<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DonationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $donorIds = DB::table('donors')->orderBy('id')->pluck('id');
        $donorOneId = $donorIds->get(0);
        $donorTwoId = $donorIds->get(1) ?? $donorOneId;
        $donorThreeId = $donorIds->get(2) ?? $donorTwoId;

        DB::table('donations')->insert([
            [
                'donor_id' => $donorOneId,
                'pad_count' => 200,
                'pledge_date' => now()->subDays(5)->toDateString(),
                'expected_delivery_date' => now()->addDays(5)->toDateString(),
                'fulfillment_date' => null,
                'notes' => 'Community-led drive',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'donor_id' => $donorTwoId,
                'pad_count' => 450,
                'pledge_date' => now()->subDays(7)->toDateString(),
                'expected_delivery_date' => now()->addDays(3)->toDateString(),
                'fulfillment_date' => null,
                'notes' => 'Quarterly commitment',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'donor_id' => $donorThreeId,
                'pad_count' => 300,
                'pledge_date' => now()->subDays(3)->toDateString(),
                'expected_delivery_date' => now()->addDays(10)->toDateString(),
                'fulfillment_date' => null,
                'notes' => 'Emergency top-up',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
