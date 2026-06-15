<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuditLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('audit_logs')->insert([
            'user_id' => 1,
            'user_role' => 'Admin',
            'action_performed' => 'Seeded baseline data',
            'ip_address' => '127.0.0.1',
        ]);

        DB::table('audit_logs')->insert([
            'user_id' => 2,
            'user_role' => 'Coordinator',
            'action_performed' => 'Seeded baseline data',
            'ip_address' => '127.0.0.1',
        ]);
    }
}
