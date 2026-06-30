<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('volunteer_interests');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('volunteer_interests', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
};
