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
        if (!Schema::hasTable('schools')) {
            return;
        }

        DB::transaction(function () {
            $duplicateGroups = DB::table('schools')
                ->select('school_name', DB::raw('COUNT(*) as total'))
                ->groupBy('school_name')
                ->having('total', '>', 1)
                ->get();

            foreach ($duplicateGroups as $group) {
                $schoolName = (string) $group->school_name;

                $ids = DB::table('schools')
                    ->where('school_name', $schoolName)
                    ->orderBy('school_id')
                    ->pluck('school_id')
                    ->map(fn ($id) => (int) $id)
                    ->all();

                if (count($ids) < 2) {
                    continue;
                }

                $canonicalId = $ids[0];
                $duplicateIds = array_slice($ids, 1);

                DB::table('users')
                    ->whereIn('school_id', $duplicateIds)
                    ->update(['school_id' => $canonicalId]);

                foreach ($duplicateIds as $duplicateId) {
                    // Prevent unique key collisions on (school_id, academic_year, month)
                    // before we remap duplicate school enrollments to the canonical school.
                    DB::statement(
                        'DELETE e FROM enrollments e
                         INNER JOIN enrollments keep_row
                            ON keep_row.school_id = ?
                           AND keep_row.academic_year = e.academic_year
                           AND keep_row.month = e.month
                         WHERE e.school_id = ?',
                        [$canonicalId, $duplicateId]
                    );
                }

                DB::table('enrollments')
                    ->whereIn('school_id', $duplicateIds)
                    ->update(['school_id' => $canonicalId]);

                DB::table('shortfall_reports')
                    ->whereIn('school_id', $duplicateIds)
                    ->update(['school_id' => $canonicalId]);

                DB::table('distributions')
                    ->whereIn('school_id', $duplicateIds)
                    ->update(['school_id' => $canonicalId]);

                DB::table('schools')
                    ->whereIn('school_id', $duplicateIds)
                    ->delete();
            }
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->unique('school_name', 'schools_school_name_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropUnique('schools_school_name_unique');
        });
    }
};
