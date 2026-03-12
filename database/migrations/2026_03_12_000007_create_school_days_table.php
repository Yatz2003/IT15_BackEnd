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
        Schema::create('school_days', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->decimal('attendance_rate', 5, 2)->default(0);
            $table->boolean('is_holiday')->default(false);
            $table->timestamps();

            $table->index(['date', 'is_holiday']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_days');
    }
};
