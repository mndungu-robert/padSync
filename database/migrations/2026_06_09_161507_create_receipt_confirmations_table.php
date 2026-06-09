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
        Schema::create('receipt_confirmations', function (Blueprint $table) {
            $table->id('confirmation_id');
            $table->foreignId('distribution_id')->unique()->constrained('distributions', 'distribution_id')->onDelete('cascade');
            $table->foreignId('coordinator_id')->constrained('users');
            $table->integer('received_quantity');
            $table->timestamp('confirmation_date')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_confirmations');
    }
};
