<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistributionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schoolIds = DB::table('schools')->orderBy('school_id')->pluck('school_id');
        $schoolOneId = $schoolIds->get(0);
        $schoolTwoId = $schoolIds->get(1) ?? $schoolOneId;
        $schoolThreeId = $schoolIds->get(2) ?? $schoolTwoId;

        DB::table('distributions')->insert([
            [
                'school_id' => $schoolOneId,
                'quantity_distributed' => 180,
                'distribution_date' => now()->toDateString(),
                'status' => 'Dispatched',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'school_id' => $schoolTwoId,
                'quantity_distributed' => 220,
                'distribution_date' => now()->subDay()->toDateString(),
                'status' => 'Pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'school_id' => $schoolThreeId,
                'quantity_distributed' => 200,
                'distribution_date' => now()->subDays(2)->toDateString(),
                'status' => 'Received',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
