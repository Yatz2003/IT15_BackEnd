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
        if (! Schema::hasColumn('subjects', 'subject_code')) {
            return;
        }

        DB::table('subjects')
            ->whereNull('subject_code')
            ->orWhere('subject_code', '')
            ->orderBy('id')
            ->get(['id'])
            ->each(function (object $subject): void {
                DB::table('subjects')
                    ->where('id', $subject->id)
                    ->update(['subject_code' => 'SUBJ'.$subject->id]);
            });

        $duplicateIds = DB::table('subjects as newer')
            ->join('subjects as older', function ($join): void {
                $join->on('newer.subject_code', '=', 'older.subject_code')
                    ->whereColumn('newer.id', '>', 'older.id');
            })
            ->pluck('newer.id');

        if ($duplicateIds->isNotEmpty()) {
            DB::table('subjects')->whereIn('id', $duplicateIds)->delete();
        }

        Schema::table('subjects', function (Blueprint $table): void {
            $table->unique('subject_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table): void {
            $table->dropUnique('subjects_subject_code_unique');
        });
    }
};
