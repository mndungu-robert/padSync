<?php

namespace Database\Seeders;

use App\Models\Donor;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            UserSeeder::class,
            DonorSeeder::class,
        ]);

        // 2. Second Admin Account
        User::create([
            'name' => 'System Admin Two',
            'email' => 'admin2@padsync.com',
            'username' => 'admin2',
            'password' => Hash::make('password123'),
            'role' => 'Admin',
            'status' => 'Approved',
        ]);

        // 3. Program Manager Account
        User::create([
            'name' => 'Project Manager One',
            'email' => 'manager1@padsync.com',
            'username' => 'manager1',
            'password' => Hash::make('password123'),
            'role' => 'Program Manager',
            'status' => 'Approved',
        ]);

        // --- DUMMY SCHOOLS SCOPE ---
        $schools = [
            ['school_name' => 'St. Mary Primary School', 'school_location' => 'Zone A', 'enrollment' => 350],
            ['school_name' => 'Hillcrest Secondary School', 'school_location' => 'Zone B', 'enrollment' => 520],
            ['school_name' => 'Greenwood Girls Academy', 'school_location' => 'Zone A', 'enrollment' => 280],
            ['school_name' => 'Riverside Community School', 'school_location' => 'Zone C', 'enrollment' => 410],
        ];

        foreach ($schools as $schoolData) {
            $schoolId = DB::table('schools')->insertGetId([
                'school_name' => $schoolData['school_name'],
                'school_location' => $schoolData['school_location'],
                'enrollment' => $schoolData['enrollment'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ], 'school_id');

            // --- DUMMY INVENTORY UNITS (For global hub summaries) ---
            DB::table('inventories')->insert([
                'quantity_available' => rand(150, 1200),
                'allocated_stock' => rand(20, 100),
                'reorder_level' => 100,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // --- DUMMY URGENT SHORTFALLS (For your admin action tables) ---
            $requiredPads = rand(200, 800);
            $availablePads = rand(0, $requiredPads - 1);
            DB::table('shortfall_reports')->insert([
                'school_id' => $schoolId,
                'report_date' => Carbon::now()->subDays(rand(1, 10))->toDateString(),
                'required_pads' => $requiredPads,
                'available_pads' => $availablePads,
                'shortfall' => $requiredPads - $availablePads,
                'status' => collect(['Draft', 'Submitted', 'Dispatched', 'Received'])->random(),
                'created_at' => Carbon::now()->subDays(rand(1, 10)),
                'updated_at' => Carbon::now(),
            ]);
        }

        // --- DUMMY DONATION PLEDGES (For your financial counter card) ---
        $donorEmails = ['pledge1@example.com', 'sponsor@globalaid.org', 'support@padsfoundation.net'];
        foreach ($donorEmails as $index => $email) {
            $donor = Donor::firstOrCreate(
                ['email' => $email],
                [
                    'name' => 'Global Benefactor '.($index + 1),
                    'phone' => null,
                    'donor_type' => 'Individual',
                    'organization_name' => null,
                ]
            );

            DB::table('donations')->insert([
                'donor_id' => $donor->donor_id,
                'pad_count' => collect([250, 500, 150, 1000])->random(),
                'pledge_date' => Carbon::now()->subDays(rand(2, 15))->toDateString(),
                'pledge_status' => 'Pledged',
                'created_at' => Carbon::now()->subDays(rand(2, 15)),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
