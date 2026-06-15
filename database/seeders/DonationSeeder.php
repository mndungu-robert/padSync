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

        DB::table('donations')->insert([
            'donor_id' => $donorOneId,
            'pad_count' => 200,
            'pledge_date' => now()->toDateString(),
            'pledge_status' => 'Pledged',
            'quantity_amount' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('donations')->insert([
            'donor_id' => $donorTwoId,
            'pad_count' => 450,
            'pledge_date' => now()->subDays(2)->toDateString(),
            'pledge_status' => 'Partially Received',
            'quantity_amount' => 150,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
