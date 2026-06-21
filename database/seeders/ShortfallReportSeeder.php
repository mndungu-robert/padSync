<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShortfallReportSeeder extends Seeder
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

        DB::table('shortfall_reports')->insert([
            [
                'school_id' => $schoolOneId,
                'report_date' => now()->toDateString(),
                'required_pads' => 300,
                'available_pads' => 120,
                'government_pads_received' => 40,
                'shortfall' => 140,
                'status' => 'Submitted',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'school_id' => $schoolTwoId,
                'report_date' => now()->subDay()->toDateString(),
                'required_pads' => 260,
                'available_pads' => 90,
                'government_pads_received' => 35,
                'shortfall' => 135,
                'status' => 'Draft',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'school_id' => $schoolThreeId,
                'report_date' => now()->subDays(2)->toDateString(),
                'required_pads' => 280,
                'available_pads' => 105,
                'government_pads_received' => 30,
                'shortfall' => 145,
                'status' => 'Received',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
