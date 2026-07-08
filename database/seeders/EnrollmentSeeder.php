<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnrollmentSeeder extends Seeder
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

        $timestamp = now();

        DB::table('enrollments')->upsert([
            [
                'school_id' => $schoolOneId,
                'girl_count' => 320,
                'government_pads_received' => 80,
                'academic_year' => '2026',
                'month' => 'June',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'school_id' => $schoolTwoId,
                'girl_count' => 240,
                'government_pads_received' => 60,
                'academic_year' => '2026',
                'month' => 'June',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'school_id' => $schoolThreeId,
                'girl_count' => 290,
                'government_pads_received' => 75,
                'academic_year' => '2026',
                'month' => 'June',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ], ['school_id', 'academic_year', 'month'], ['girl_count', 'government_pads_received', 'updated_at']);
    }
}
