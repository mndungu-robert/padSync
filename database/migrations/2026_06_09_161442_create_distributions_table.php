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
        Schema::create('distributions', function (Blueprint $table) {
            $table->id('distribution_id');
            $table->foreignId('school_id')->constrained('schools', 'school_id')->onDelete('cascade');
            $table->integer('quantity_distributed');
            $table->date('distribution_date');
            $table->enum('status', ['Pending', 'Dispatched', 'Received'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributions');
    }
};
