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
        $hasDayName = Schema::hasColumn('school_days', 'day_name');
        $hasEventName = Schema::hasColumn('school_days', 'event_name');

        Schema::table('school_days', function (Blueprint $table) use ($hasDayName, $hasEventName) {
            if (! $hasDayName) {
                $table->string('day_name')->after('date');
            }

            if (! $hasEventName) {
                $table->string('event_name')->nullable()->after('is_holiday');
            }

            $table->integer('attendance_rate')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $hasDayName = Schema::hasColumn('school_days', 'day_name');
        $hasEventName = Schema::hasColumn('school_days', 'event_name');

        Schema::table('school_days', function (Blueprint $table) use ($hasDayName, $hasEventName) {
            $table->decimal('attendance_rate', 5, 2)->default(0)->change();

            if ($hasEventName) {
                $table->dropColumn('event_name');
            }

            if ($hasDayName) {
                $table->dropColumn('day_name');
            }
        });
    }
};
