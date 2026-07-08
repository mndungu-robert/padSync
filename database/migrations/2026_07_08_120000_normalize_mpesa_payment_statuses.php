<?php

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
        if (!Schema::hasColumn('donations', 'payment_status')) {
            return;
        }

        DB::table('donations')
            ->where('contribution_type', 'Donate Money')
            ->where('payment_status', 'Completed')
            ->update(['payment_status' => 'Successful']);

        DB::table('donations')
            ->where('contribution_type', 'Donate Money')
            ->where('payment_status', 'Pending')
            ->update(['payment_status' => 'Failed']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('donations', 'payment_status')) {
            return;
        }

        DB::table('donations')
            ->where('contribution_type', 'Donate Money')
            ->where('payment_status', 'Successful')
            ->update(['payment_status' => 'Completed']);
    }
};
