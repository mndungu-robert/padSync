<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Keep the latest record per school/year/month before adding uniqueness.
        DB::statement(
            'DELETE e1 FROM enrollments e1
             INNER JOIN enrollments e2
               ON e1.school_id = e2.school_id
              AND e1.academic_year = e2.academic_year
              AND e1.month = e2.month
              AND e1.enrollment_id < e2.enrollment_id'
        );

        Schema::table('enrollments', function (Blueprint $table) {
            $table->unique(['school_id', 'academic_year', 'month'], 'enrollments_school_year_month_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropUnique('enrollments_school_year_month_unique');
        });
    }
};
