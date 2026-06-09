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
        Schema::create('donations', function (Blueprint $table) {
            $table->id('donation_id');
            $table->foreignId('donor_id')->constrained('users')->onDelete('cascade');
            $table->integer('pad_count');
            $table->date('pledge_date');
            $table->enum('pledge_status', ['Pledged', 'Partially Received', 'Fully Received', 'Cancelled'])->default('Pledged');
            $table->date('fulfillment_date')->nullable(); // Tracked when the physical items arrive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
