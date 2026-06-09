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
        Schema::create('shortfall_reports', function (Blueprint $table) {
            $table->id('report_id');
            $table->foreignId('school_id')->constrained('schools', 'school_id')->onDelete('cascade');
            $table->date('report_date');
            $table->integer('required_pads');
            $table->integer('available_pads');
            $table->integer('shortfall');
            $table->enum('status', ['Draft', 'Submitted', 'Dispatched', 'Received'])->default('Submitted');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shortfall_reports');
    }
};
