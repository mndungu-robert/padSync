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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id('enrollment_id');
            $table->foreignId('school_id')->constrained('schools', 'school_id')->onDelete('cascade');
            $table->integer('girl_count'); // Tracks the targeted number of girls needing pads
            $table->string('academic_year', 10);    // e.g., "2026", "2026/2027"
            $table->string('month', 20)->nullable(); // To track variations per term/month
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
