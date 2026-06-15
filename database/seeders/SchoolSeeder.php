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
        DB::table('schools')->insert([
            'school_name' => 'Nairobi High School',
            'school_location' => 'Nairobi, Kenya',
            'enrollment' => 640,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('schools')->insert([
            'school_name' => 'Kibera Secondary School',
            'school_location' => 'Kibera, Nairobi',
            'enrollment' => 480,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
