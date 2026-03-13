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
        if (! Schema::hasColumn('students', 'program_id')) {
            Schema::table('students', function (Blueprint $table): void {
                $table->foreignId('program_id')
                    ->nullable()
                    ->after('course_id')
                    ->constrained('courses')
                    ->cascadeOnDelete();
            });
        }

        DB::table('students')
            ->whereNull('program_id')
            ->update(['program_id' => DB::raw('course_id')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('students', 'program_id')) {
            Schema::table('students', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('program_id');
            });
        }
    }
};
