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
        $hasSubjectCode = Schema::hasColumn('subjects', 'subject_code');
        $hasUnits = Schema::hasColumn('subjects', 'units');
        $hasSemester = Schema::hasColumn('subjects', 'semester');

        Schema::table('subjects', function (Blueprint $table) use ($hasSubjectCode, $hasUnits, $hasSemester): void {
            if (! $hasSubjectCode) {
                $table->string('subject_code')->nullable()->after('id');
            }

            if (! $hasUnits) {
                $table->unsignedTinyInteger('units')->default(3)->after('program_id');
            }

            if (! $hasSemester) {
                $table->string('semester')->default('First')->after('units');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $hasSubjectCode = Schema::hasColumn('subjects', 'subject_code');
        $hasUnits = Schema::hasColumn('subjects', 'units');
        $hasSemester = Schema::hasColumn('subjects', 'semester');

        Schema::table('subjects', function (Blueprint $table) use ($hasSubjectCode, $hasUnits, $hasSemester): void {
            if ($hasSemester) {
                $table->dropColumn('semester');
            }

            if ($hasUnits) {
                $table->dropColumn('units');
            }

            if ($hasSubjectCode) {
                $table->dropColumn('subject_code');
            }
        });
    }
};
