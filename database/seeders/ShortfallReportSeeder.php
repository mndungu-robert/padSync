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

        DB::table('shortfall_reports')->insert([
            'school_id' => $schoolOneId,
            'report_date' => now()->toDateString(),
            'required_pads' => 300,
            'available_pads' => 120,
            'shortfall' => 180,
            'status' => 'Submitted',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('shortfall_reports')->insert([
            'school_id' => $schoolTwoId,
            'report_date' => now()->subDay()->toDateString(),
            'required_pads' => 260,
            'available_pads' => 90,
            'shortfall' => 170,
            'status' => 'Draft',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
