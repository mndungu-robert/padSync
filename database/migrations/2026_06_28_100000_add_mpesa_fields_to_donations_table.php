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
            if (!Schema::hasColumn('donations', 'amount_kes')) {
                $table->decimal('amount_kes', 10, 2)->default(0)->after('pad_count');
            }

            if (!Schema::hasColumn('donations', 'payment_method')) {
                $table->string('payment_method')->default('M-Pesa')->after('amount_kes');
            }

            if (!Schema::hasColumn('donations', 'payment_status')) {
                $table->string('payment_status')->default('Pending')->after('payment_method');
            }

            if (!Schema::hasColumn('donations', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('payment_status');
            }

            if (!Schema::hasColumn('donations', 'merchant_request_id')) {
                $table->string('merchant_request_id')->nullable()->after('payment_reference');
            }

            if (!Schema::hasColumn('donations', 'checkout_request_id')) {
                $table->string('checkout_request_id')->nullable()->index()->after('merchant_request_id');
            }

            if (!Schema::hasColumn('donations', 'payer_phone')) {
                $table->string('payer_phone')->nullable()->after('checkout_request_id');
            }

            if (!Schema::hasColumn('donations', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payer_phone');
            }

            if (!Schema::hasColumn('donations', 'callback_payload')) {
                $table->json('callback_payload')->nullable()->after('paid_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $columns = [
                'amount_kes',
                'payment_method',
                'payment_status',
                'payment_reference',
                'merchant_request_id',
                'checkout_request_id',
                'payer_phone',
                'paid_at',
                'callback_payload',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('donations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
