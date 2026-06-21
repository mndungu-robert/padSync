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
            [
                'user_id' => 1,
                'user_role' => 'Admin',
                'action_performed' => 'Seeded baseline data',
                'ip_address' => '127.0.0.1',
                'created_at' => now(),
            ],
            [
                'user_id' => 2,
                'user_role' => 'Program Manager',
                'action_performed' => 'Reviewed June donation commitments',
                'ip_address' => '127.0.0.1',
                'created_at' => now()->subMinutes(10),
            ],
            [
                'user_id' => 3,
                'user_role' => 'Coordinator',
                'action_performed' => 'Confirmed school receipt for dispatched pads',
                'ip_address' => '127.0.0.1',
                'created_at' => now()->subMinutes(20),
            ],
        ]);
    }
}
