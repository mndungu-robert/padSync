<?php

namespace Database\Seeders;

use App\Models\ReceiptConfirmation;
use Illuminate\Database\Seeder;

class ReceiptConfirmationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        ReceiptConfirmation::create([
            'donation_id' => 1,
            'confirmed_by' => 'Jane Doe',
            'confirmation_date' => now(),
        ]);
        ReceiptConfirmation::create([
            'donation_id' => 2,
            'confirmed_by' => 'John Smith',
            'confirmation_date' => now(),
        ]);
    }
}
