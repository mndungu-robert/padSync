<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. Add the school_id column (nullable because Admin/PM don't have schools)
            $table->foreignId('school_id')
                  ->nullable()
                  ->after('role')
                  ->constrained('schools', 'school_id')
                  ->onDelete('set null'); // Keeps user if a school is deleted
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
        });
    }
};
