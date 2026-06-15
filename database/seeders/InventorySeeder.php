<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('inventories')->insert([
            'quantity_available' => 1000,
            'allocated_stock' => 150,
            'reorder_level' => 100,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('inventories')->insert([
            'quantity_available' => 700,
            'allocated_stock' => 90,
            'reorder_level' => 120,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
