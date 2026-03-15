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
        $hasProgramId = Schema::hasColumn('enrollments', 'program_id');
        $hasAcademicYear = Schema::hasColumn('enrollments', 'academic_year');
        $hasSemester = Schema::hasColumn('enrollments', 'semester');
        $hasStatus = Schema::hasColumn('enrollments', 'status');

        Schema::table('enrollments', function (Blueprint $table) use ($hasProgramId, $hasAcademicYear, $hasSemester, $hasStatus): void {
            if (! $hasProgramId) {
                $table->foreignId('program_id')->nullable()->after('subject_id')->constrained('programs')->cascadeOnDelete();
            }

            if (! $hasAcademicYear) {
                $table->string('academic_year', 20)->nullable()->after('program_id');
            }

            if (! $hasSemester) {
                $table->string('semester', 20)->nullable()->after('academic_year');
            }

            if (! $hasStatus) {
                $table->string('status', 20)->default('Enrolled')->after('semester');
            }
        });

        if (! $hasProgramId) {
            DB::table('enrollments')
                ->join('students', 'students.id', '=', 'enrollments.student_id')
                ->update(['enrollments.program_id' => DB::raw('students.program_id')]);
        }

        if (! $hasAcademicYear) {
            DB::table('enrollments')->whereNull('academic_year')->update(['academic_year' => '2025']);
        }

        if (! $hasSemester) {
            DB::table('enrollments')->whereNull('semester')->update(['semester' => 'First']);
        }

        if (! $hasStatus) {
            DB::table('enrollments')->whereNull('status')->update(['status' => 'Enrolled']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $hasProgramId = Schema::hasColumn('enrollments', 'program_id');
        $hasAcademicYear = Schema::hasColumn('enrollments', 'academic_year');
        $hasSemester = Schema::hasColumn('enrollments', 'semester');
        $hasStatus = Schema::hasColumn('enrollments', 'status');

        Schema::table('enrollments', function (Blueprint $table) use ($hasProgramId, $hasAcademicYear, $hasSemester, $hasStatus): void {
            if ($hasProgramId) {
                $table->dropConstrainedForeignId('program_id');
            }

            if ($hasStatus) {
                $table->dropColumn('status');
            }

            if ($hasSemester) {
                $table->dropColumn('semester');
            }

            if ($hasAcademicYear) {
                $table->dropColumn('academic_year');
            }
        });
    }
};
