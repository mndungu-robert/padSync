<?php

use App\Support\PhonePrivacy;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('donations', 'payer_phone')) {
            return;
        }

        DB::table('donations')
            ->select(['donation_id', 'payer_phone'])
            ->whereNotNull('payer_phone')
            ->orderBy('donation_id')
            ->chunkById(200, function ($rows): void {
                foreach ($rows as $row) {
                    $hashed = PhonePrivacy::hash((string) $row->payer_phone);

                    DB::table('donations')
                        ->where('donation_id', $row->donation_id)
                        ->update(['payer_phone' => $hashed]);
                }
            }, 'donation_id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // One-way hash cannot be reversed.
    }
};
