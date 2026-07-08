<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timestamp = now();

        $schools = [
            [
                'school_name' => 'Dagoretti Mixed Secondary',
                'school_location' => 'Dagoretti, Nairobi',
                'enrollment' => 640,
                'updated_at' => $timestamp,
            ],
            [
                'school_name' => 'Kibera Primary School',
                'school_location' => 'Kibera, Nairobi',
                'enrollment' => 480,
                'updated_at' => $timestamp,
            ],
            [
                'school_name' => 'Komarock Girls Secondary',
                'school_location' => 'Komarock, Nairobi',
                'enrollment' => 530,
                'updated_at' => $timestamp,
            ],
        ];

        foreach ($schools as $school) {
            DB::table('schools')->updateOrInsert(
                ['school_name' => $school['school_name']],
                $school + ['created_at' => $timestamp]
            );
        }
    }
}
