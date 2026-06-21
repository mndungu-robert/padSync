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
            [
                'school_name' => 'Dagoretti Mixed Secondary',
                'school_location' => 'Dagoretti, Nairobi',
                'enrollment' => 640,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'school_name' => 'Kibera Primary School',
                'school_location' => 'Kibera, Nairobi',
                'enrollment' => 480,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'school_name' => 'Komarock Girls Secondary',
                'school_location' => 'Komarock, Nairobi',
                'enrollment' => 530,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
