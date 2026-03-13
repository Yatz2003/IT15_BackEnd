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
        if (! Schema::hasColumn('subjects', 'program_id')) {
            Schema::table('subjects', function (Blueprint $table): void {
                $table->foreignId('program_id')
                    ->nullable()
                    ->after('subject_name')
                    ->constrained('courses')
                    ->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('subjects', 'program_id')) {
            Schema::table('subjects', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('program_id');
            });
        }
    }
};
