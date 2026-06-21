<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            if (Schema::hasColumn('donations', 'pledge_status')) {
                $table->dropColumn('pledge_status');
            }

            if (Schema::hasColumn('donations', 'quantity_amount')) {
                $table->dropColumn('quantity_amount');
            }

            if (Schema::hasColumn('donations', 'received_count')) {
                $table->dropColumn('received_count');
            }
        });

        Schema::table('donors', function (Blueprint $table) {
            if (!Schema::hasColumn('donors', 'pad_count')) {
                $table->integer('pad_count')->default(0)->after('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            if (!Schema::hasColumn('donations', 'pledge_status')) {
                $table->string('pledge_status')->default('Pledged')->after('pledge_date');
            }

            if (!Schema::hasColumn('donations', 'quantity_amount')) {
                $table->integer('quantity_amount')->default(0)->after('fulfillment_date');
            }

            if (!Schema::hasColumn('donations', 'received_count')) {
                $table->integer('received_count')->default(0)->after('quantity_amount');
            }
        });

        Schema::table('donors', function (Blueprint $table) {
            if (Schema::hasColumn('donors', 'pad_count')) {
                $table->dropColumn('pad_count');
            }
        });
    }
};
